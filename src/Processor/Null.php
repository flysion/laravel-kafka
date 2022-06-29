<?php

namespace Flysion\Kafka\Processor;

class Null
{
    /**
     * @param mixed $data
     * @param array $options
     * @param \Rdkafka\Message $message
     * @param string $connection kafka 连接
     * @return mixed
     */
    public function process($data, $options, $message, $connection)
    {
        // pass
    }
}
