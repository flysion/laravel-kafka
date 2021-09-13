<?php

namespace Flysion\Kafka\Events;

class Stats
{
    /**
     * @var \Rdkafka|\Flysion\Kafka\Consumer|\Flysion\Kafka\HighConsumer|\Flysion\Kafka\Producer
     */
    public $kafka;

    /**
     * @var array
     */
    public $stats;
}