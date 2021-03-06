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
                'enable.auto.commit' => "false",
                'statistics.interval.ms' => 1000,
                'request.timeout.ms' => 20000,
                'log.connection.close' => "false",
                'auto.offset.reset' => 'earliest',
//                'debug' => 'all',
                'onStats' => [

                ],
            ],
            'topics' => [

            ]
        ],
    ]
];