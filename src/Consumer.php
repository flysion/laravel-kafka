<?php

namespace Flysion\Kafka;

/**
 * @link http://kafka.apache.org/documentation/
 * @link https://github.com/arnaud-lb/php-rdkafka
 * @link https://github.com/edenhill/librdkafka/blob/master/CONFIGURATION.md
 * @link https://arnaud.le-blanc.net/php-rdkafka-doc/phpdoc/class.rdkafka-consumer.html
 */
class Consumer extends \Rdkafka\Consumer
{
    /**
     * @param Conf $conf
     */
    public function __construct($conf)
    {
        parent::__construct($conf);
    }
}