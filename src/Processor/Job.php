<?php

namespace Flysion\Kafka\Processor;

class Job
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
        $class = $options['job'];
        $job = $class::dispatch($message);

        if(isset($options['connection'])) {
            $job->onConnection($options['connection']);
        }

        if(isset($options['queue'])) {
            $job->onQueue($options['queue']);
        }
    }
}
