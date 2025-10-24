<?php

namespace BackdropS3FS\Aws\Arn\S3;

use BackdropS3FS\Aws\Arn\ArnInterface;
/**
 * @internal
 */
interface BucketArnInterface extends ArnInterface
{
    public function getBucketName();
}
