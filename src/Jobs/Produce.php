<?php

namespace Flysion\Kafka\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class Produce implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable;

    /**
     * @var string
     */
    protected $producerName;

    /**
     * @var string
     */
    protected $topicName;

    /**
     * @var string
     */
    protected $payload;

    /**
     * @var int
     */
    protected $partition;

    /**
     * @var int
     */
    protected $msgflags;

    /**
     * @var string
     */
    protected $key;

    /**
     * @var string
     */
    protected $opaque;

    /**
     * @param string $producerName
     * @param string $topicName
     * @param string $payload
     * @param int $partition
     * @param int $msgflags
     * @param string $key
     * @param string $opaque
     */
    public function __construct($producerName, $topicName, $partition, $msgflags, $payload, $key, $opaque)
    {
        $this->producerName = $producerName;
        $this->topicName = $topicName;
        $this->payload = $payload;
        $this->partition = $partition;
        $this->msgflags = $msgflags;
        $this->key = $key;
        $this->opaque = $opaque;
    }

    /**
     * @return string
     */
    public function displayName()
    {
        return get_class($this) . " -> {$this->producerName}.{$this->topicName}";
    }

    /**
     * handle a job
     */
    public function handle()
    {
        $producerTopic = \Flysion\Kafka\kafka_producer_topic("{$this->producerName}.{$this->topicName}");

        $producerTopic->produce(
            $this->partition,
            $this->msgflags,
            $this->payload,
            $this->key,
            $this->opaque
        );

        // 指定调用将阻塞等待事件的最长时间（以毫秒为单位）。对于非阻塞调用，提供 0 作为timeout_ms。要无限期地等待事件，请提供 -1。
        $producerTopic->producer()->poll(0);
    }
}