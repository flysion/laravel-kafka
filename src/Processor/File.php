<?php

namespace Flysion\Kafka\Processor;

class File
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
        file_put_contents($options['file'], \Flysion\Kafka\json_encode($message) . "\n");
    }
}
