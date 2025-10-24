<?php

namespace BackdropS3FS\Aws\Api\Parser;

use BackdropS3FS\Aws\Api\Service;
use BackdropS3FS\Aws\Api\StructureShape;
use BackdropS3FS\Aws\CommandInterface;
use BackdropS3FS\Aws\ResultInterface;
use BackdropS3FS\Psr\Http\Message\ResponseInterface;
use BackdropS3FS\Psr\Http\Message\StreamInterface;
/**
 * @internal
 */
abstract class AbstractParser
{
    /** @var \Aws\Api\Service Representation of the service API*/
    protected $api;
    /** @var callable */
    protected $parser;
    /**
     * @param Service $api Service description.
     */
    public function __construct(Service $api)
    {
        $this->api = $api;
    }
    /**
     * @param CommandInterface  $command  Command that was executed.
     * @param ResponseInterface $response Response that was received.
     *
     * @return ResultInterface
     */
    abstract public function __invoke(CommandInterface $command, ResponseInterface $response);
    abstract public function parseMemberFromStream(StreamInterface $stream, StructureShape $member, $response);
}
