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
    protected $topic;

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
     * @param string $producerName
     * @param string $topic
     * @param string $payload
     * @param int $partition
     * @param int $msgflags
     * @param string $key
     * @param string $opaque
     */
    public function __construct($producerName, $topic, $partition, $msgflags, $payload, $key)
    {
        $this->producerName = $producerName;
        $this->topic = $topic;
        $this->payload = $payload;
        $this->partition = $partition;
        $this->msgflags = $msgflags;
        $this->key = $key;
    }

    public function handle()
    {
        \Flysion\Kafka\kafka_producer_topic("{$this->producerName}.{$this->topic}")->produce(
            $this->partition,
            $this->msgflags,
            $this->payload,
            $this->key
        );
    }
}