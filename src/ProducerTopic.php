<?php

namespace Flysion\Kafka;

/**
 * @package Flysion\Kafka
 * @mixin \Rdkafka\ProducerTopic
 */
class ProducerTopic
{
    /**
     * @var Producer
     */
    private $producer;

    /**
     * @var \Rdkafka\ProducerTopic
     */
    private $producerTopic;

    /**
     * @param Producer $producer
     * @param \Rdkafka\ProducerTopic $producerTopic
     */
    public function __construct($producer, $producerTopic)
    {
        $this->producer = $producer;
        $this->producerTopic = $producerTopic;
    }

    /**
     * @return Producer
     */
    public function producer()
    {
        return $this->producer;
    }

    /**
     * @param $partition
     * @param $msgflags
     * @param null|string $payload
     * @param null|string $key
     * @param null|string $opaque
     * @return \Illuminate\Foundation\Bus\PendingDispatch
     */
    public function produceToQueue($partition, $msgflags, $payload = null, $key = null, $opaque = null)
    {
        return \Flysion\Kafka\Jobs\Produce::dispatch(
            $this->producer->name,
            $this->getName()/* \RdKafka\Topic::getName */,
            $partition,
            $msgflags,
            $payload,
            $key,
            $opaque
        );
    }

    /**
     * @param $partition
     * @param $msgflags
     * @param null|string $payload
     * @param null|string $key
     * @return void
     */
    public function produce($partition, $msgflags, $payload = null, $key = null, $opaque = null)
    {
        app('events')->dispatch(new \Flysion\Kafka\Events\Produce(
            $this->producer,
            $this,
            $payload,
            $msgflags,
            $payload,
            $key,
            $opaque
        ));

        return $this->producerTopic->produce($partition, $msgflags, $payload, $key, $opaque);
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