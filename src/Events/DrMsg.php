<?php

namespace Flysion\Kafka\Events;

class DrMsg
{
    /**
     * @var \Rdkafka|\Flysion\Kafka\Consumer|\Flysion\Kafka\HighConsumer|\Flysion\Kafka\Producer
     */
    public $kafka;

    /**
     * @var string
     */
    public $message;
}