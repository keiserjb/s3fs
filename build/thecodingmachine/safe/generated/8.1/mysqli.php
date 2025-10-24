<?php

namespace BackdropS3FS\Safe;

use BackdropS3FS\Safe\Exceptions\MysqliException;
/**
 * Returns client per-process statistics.
 *
 * @return array Returns an array with client stats if success.
 * @throws MysqliException
 *
 */
function mysqli_get_client_stats(): array
{
    error_clear_last();
    $safeResult = \mysqli_get_client_stats();
    if ($safeResult === \false) {
        throw MysqliException::createFromPhpError();
    }
    return $safeResult;
}
