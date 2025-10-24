<?php

namespace BackdropS3FS\GuzzleHttp;

use BackdropS3FS\Psr\Http\Message\MessageInterface;
interface BodySummarizerInterface
{
    /**
     * Returns a summarized message body.
     */
    public function summarize(MessageInterface $message): ?string;
}
