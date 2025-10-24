<?php

/**
 * @file
 * Minimal PSR-4 autoloader for scoped AWS SDK in s3fs.
 *
 * This avoids using the Composer-generated autoloader which has static state
 * issues when require_once'd multiple times.
 */

if (defined('S3FS_AWS_BOOTSTRAPPED')) {
  return;
}

// SCOPED BUILD NOT YET FUNCTIONAL
// s3fs extends Aws\S3\StreamWrapper which must exist at parse time.
// The scoped build requires more work to resolve parse-time class resolution.
// Until complete, s3fs must use composer_manager's global vendor directory.
//
// To enable s3fs:
// 1. Install composer_manager module
// 2. Run: composer require aws/aws-sdk-php
// 3. s3fs will use the global vendor directory automatically

define('S3FS_AWS_BOOTSTRAPPED', 1);
