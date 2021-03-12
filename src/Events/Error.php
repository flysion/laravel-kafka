<?php

namespace Flysion\Kafka\Events;

class Error
{
    /**
     * @var
     */
    public $kafka;

    /**
     * @var int
     */
    public $err;

    /**
     * @var string
     */
    public $reason;
}