<?php

namespace Flysion\Kafka\Deserializer;

class Json
{
    /**
     * @param string $payload
     * @param array $options
     * @param \Rdkafka\Message $message
     * @param string $connection kafka 连接
     * @return mixed
     */
    public function deserialize($payload, $options, $message, $connection)
    {
        return \Flysion\Kafka\json_decode($payload, true);
    }
}
