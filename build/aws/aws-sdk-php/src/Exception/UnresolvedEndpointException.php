<?php

namespace BackdropS3FS\Aws\Exception;

use BackdropS3FS\Aws\HasMonitoringEventsTrait;
use BackdropS3FS\Aws\MonitoringEventsInterface;
class UnresolvedEndpointException extends \RuntimeException implements MonitoringEventsInterface
{
    use HasMonitoringEventsTrait;
}
