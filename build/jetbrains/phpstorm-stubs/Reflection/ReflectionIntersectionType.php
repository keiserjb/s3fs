<?php

namespace BackdropS3FS;

use BackdropS3FS\JetBrains\PhpStorm\Pure;
/**
 * @since 8.1
 */
class ReflectionIntersectionType extends \ReflectionType
{
    /** @return ReflectionType[] */
    #[Pure]
    public function getTypes(): array
    {
    }
}
/**
 * @since 8.1
 */
\class_alias('BackdropS3FS\ReflectionIntersectionType', 'ReflectionIntersectionType', \false);
