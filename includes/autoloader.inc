<?php

/**
 * @file
 * Smart autoloader for AWS SDK in s3fs.
 *
 * Handles both:
 * - Composer Manager (shared global vendor)
 * - Scoped build (isolated dependencies)
 *
 * MUST be required at file parse time (top of file), not in functions.
 */

if (defined('S3FS_AUTOLOADER_LOADED')) {
  return;
}
define('S3FS_AUTOLOADER_LOADED', 1);

/**
 * Load AWS SDK autoloader and create aliases if needed.
 *
 * @return bool TRUE if autoloader loaded successfully, FALSE otherwise.
 */
function s3fs_load_autoloader() {
  static $loaded = FALSE;

  if ($loaded) {
    return TRUE;
  }

  $module_path = backdrop_get_path('module', 's3fs');

  // Try composer_manager first (recommended for production)
  if (module_exists('composer_manager')) {
    $vendor_dir = config_get('composer_manager.settings', 'vendor_dir');
    if ($vendor_dir && file_exists($vendor_dir . '/autoload.php')) {
      require_once $vendor_dir . '/autoload.php';
      watchdog('s3fs', 'Loaded dependencies from Composer Manager global vendor directory.');
      $loaded = TRUE;
      return TRUE;
    }
  }

  // Fall back to scoped build/ directory
  if (file_exists($module_path . '/build/autoload.php')) {
    require_once $module_path . '/build/autoload.php';
    watchdog('s3fs', 'Loaded dependencies from scoped vendor directory (build/).');
    // Create aliases so unscoped classes work
    _s3fs_create_class_aliases();
    $loaded = TRUE;
    return TRUE;
  }

  watchdog('s3fs', 'Could not find AWS SDK autoloader.', array(), WATCHDOG_ERROR);
  return FALSE;
}

/**
 * Get the namespace prefix for scoped classes.
 *
 * @return string The namespace prefix (e.g., 'BackdropS3FS\\') or empty string.
 */
function s3fs_get_namespace_prefix() {
  // Check if unscoped AWS is already available (composer_manager scenario)
  if (class_exists('Aws\S3\S3Client', FALSE)) {
    // Global vendor is in use, no prefix needed
    return '';
  }

  // Scoped build is in use, return the prefix
  return 'BackdropS3FS\\';
}

/**
 * Create class aliases from scoped to unscoped names.
 *
 * This MUST be called right after the scoped autoloader is required,
 * before any classes are used in extends statements.
 */
function _s3fs_create_class_aliases() {
  $prefix = 'BackdropS3FS\\';

  // List of classes/interfaces we need to alias
  $classes_to_alias = array(
    'Aws\Credentials\CredentialProvider',
    'Aws\S3\S3Client',
    'Aws\S3\StreamWrapper',           // Parent class for S3fsStreamWrapper
    'Aws\S3\Exception\S3Exception',
    'Aws\S3\S3ClientInterface',
    'Aws\CacheInterface',
    'Aws\Sdk',
  );

  foreach ($classes_to_alias as $class) {
    $scoped_class = $prefix . $class;
    $unscoped_class = $class;

    // Check if scoped class exists (without autoloading first time)
    if (!class_exists($scoped_class, FALSE) && !interface_exists($scoped_class, FALSE)) {
      // Try with autoloading enabled
      if (!class_exists($scoped_class, TRUE) && !interface_exists($scoped_class, TRUE)) {
        continue;
      }
    }

    // Only create alias if unscoped version doesn't already exist
    if (!class_exists($unscoped_class, FALSE) && !interface_exists($unscoped_class, FALSE)) {
      class_alias($scoped_class, $unscoped_class);
    }
  }
}
