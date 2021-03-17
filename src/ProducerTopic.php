<?php

namespace Flysion\Kafka;

/**
 * @package Flysion\Kafka
 * @mixin \Rdkafka\ProducerTopic
 */
class ProducerTopic
{
    /**
     * @var string
     */
    private $producerName;

    /**
     * @var \Rdkafka\ProducerTopic
     */
    private $producerTopic;

    /**
     * @param string $producerName
     * @param \Rdkafka\ProducerTopic $producerTopic
     */
    public function __construct($producerName, $producerTopic)
    {
        $this->producerName = $producerName;
        $this->producerTopic = $producerTopic;
    }

    /**
     * @param $partition
     * @param $msgflags
     * @param null|string $payload
     * @param null|string $key
     * @return \Illuminate\Foundation\Bus\PendingDispatch
     */
    public function produceToQueue($partition, $msgflags, $payload = null, $key = null)
    {
        return \Flysion\Kafka\Jobs\Produce::dispatch($this->producerName, $this->producerTopic->getName(), $partition, $msgflags, $payload, $key);
    }

    /**
     * @param $name
     * @param $arguments
     * @return mixed
     */
    public function __call($name, $arguments)
    {
        return call_user_func_array([$this->producerTopic, $name], $arguments);
    }
}