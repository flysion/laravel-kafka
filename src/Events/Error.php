<?php

namespace Flysion\Kafka\Events;

class Error
{
    /**
     * @var \Rdkafka|\Flysion\Kafka\Consumer|\Flysion\Kafka\HighConsumer|\Flysion\Kafka\Producer
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