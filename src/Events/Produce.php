<?php

namespace Flysion\Kafka\Events;

class Produce
{
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
}