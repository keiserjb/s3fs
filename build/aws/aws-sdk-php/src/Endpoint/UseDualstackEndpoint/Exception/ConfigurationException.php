<?php

namespace BackdropS3FS\Aws\Endpoint\UseDualstackEndpoint\Exception;

use BackdropS3FS\Aws\HasMonitoringEventsTrait;
use BackdropS3FS\Aws\MonitoringEventsInterface;
/**
 * Represents an error interacting with configuration for useDualstackRegion
 */
class ConfigurationException extends \RuntimeException implements MonitoringEventsInterface
{
    use HasMonitoringEventsTrait;
}
