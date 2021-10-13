<?php

namespace Flysion\Kafka\Events;

class Rebalance
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
     * @var
     */
    public $partitions;
}