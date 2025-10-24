<?php

namespace BackdropS3FS\Aws\Arn\S3;

use BackdropS3FS\Aws\Arn\ArnInterface;
/**
 * @internal
 */
interface OutpostsArnInterface extends ArnInterface
{
    public function getOutpostId();
}
