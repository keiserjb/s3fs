<?php

namespace BackdropS3FS\Aws\ClientSideMonitoring;

use BackdropS3FS\Aws\CommandInterface;
use BackdropS3FS\Aws\Exception\AwsException;
use BackdropS3FS\Aws\ResultInterface;
use BackdropS3FS\GuzzleHttp\Psr7\Request;
use BackdropS3FS\Psr\Http\Message\RequestInterface;
/**
 * @internal
 */
interface MonitoringMiddlewareInterface
{
    /**
     * Data for event properties to be sent to the monitoring agent.
     *
     * @param RequestInterface $request
     * @return array
     */
    public static function getRequestData(RequestInterface $request);
    /**
     * Data for event properties to be sent to the monitoring agent.
     *
     * @param ResultInterface|AwsException|\Exception $klass
     * @return array
     */
    public static function getResponseData($klass);
    public function __invoke(CommandInterface $cmd, RequestInterface $request);
}
