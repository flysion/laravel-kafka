<?php
/**
 * @link https://github.com/edenhill/librdkafka/blob/master/CONFIGURATION.md
 */
return [
    'default' => env('KAFKA', 'default'),
    
    'connections' => [
        'default' => [
            'config' => [
                'metadata.broker.list' => env('KAFKA_BROKERS'),
                'log_level' => "0",
//                'statistics.interval.ms' => 1000,
                'request.timeout.ms' => 10000,
                'log.connection.close' => "false",
//                'socket.timeout.ms' => 1000,
//                'debug' => 'all',

                // consumer
                'auto.offset.reset' => 'earliest',
                'enable.auto.commit' => "false",

                // producer
                'batch.num.messages' => 1000,
                'queue.buffering.max.ms' => 500, // alias linger.ms

                'onRebalance' => [
                    \Flysion\Kafka\Listeners\Rebalance::class
                ],
            ],
            'topics' => [

            ]
        ],
    ]
];