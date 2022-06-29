<?php

namespace Flysion\Kafka\Processor;

class Event
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
        $events = $options['events'] ?? 'events';

        if (!($events instanceof \Illuminate\Events\Dispatcher)) {
            $events = app($events);
        }

        $eventName = 'kafka:' . $connection . ':' . $message->topic_name;

        $events->dispatch($eventName, [$connection, $eventName, $data]);
    }
}
