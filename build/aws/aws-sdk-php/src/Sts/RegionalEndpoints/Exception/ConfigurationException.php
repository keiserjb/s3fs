<?php

namespace BackdropS3FS\Aws\Sts\RegionalEndpoints\Exception;

use BackdropS3FS\Aws\HasMonitoringEventsTrait;
use BackdropS3FS\Aws\MonitoringEventsInterface;
/**
 * Represents an error interacting with configuration for sts regional endpoints
 */
class ConfigurationException extends \RuntimeException implements MonitoringEventsInterface
{
    use HasMonitoringEventsTrait;
}
