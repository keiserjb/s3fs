<?php

// Map of unscoped => scoped class names for aliasing
$S3FS_CLASS_ALIASES = array(
  'Aws\\S3\\StreamWrapper'      => 'BackdropS3FS\\Aws\\S3\\StreamWrapper',
  'Aws\\S3\\S3ClientInterface'  => 'BackdropS3FS\\Aws\\S3\\S3ClientInterface',
  'Aws\\CacheInterface'         => 'BackdropS3FS\\Aws\\CacheInterface',
  'Aws\\S3\\Exception\\S3Exception' => 'BackdropS3FS\\Aws\\S3\\Exception\\S3Exception',
);

spl_autoload_register(static function ($class) use ($S3FS_CLASS_ALIASES) {
  if (!isset($S3FS_CLASS_ALIASES[$class])) {
    return;
  }

  $scoped = $S3FS_CLASS_ALIASES[$class];

  // Load the scoped class if it doesn't exist yet
  if (!class_exists($scoped, false) && !interface_exists($scoped, false)) {
    class_exists($scoped, true); // autoload=true to trigger autoloader
  }
  
  // Create alias if the scoped class now exists
  if (class_exists($scoped, false) || interface_exists($scoped, false)) {
    if (!class_exists($class, false) && !interface_exists($class, false)) {
      class_alias($scoped, $class);
    }
  }
}, true, true);
