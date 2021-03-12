<?php

namespace Flysion\Kafka;

/**
 * @link http://kafka.apache.org/documentation/
 * @link https://github.com/arnaud-lb/php-rdkafka
 * @link https://github.com/edenhill/librdkafka/blob/master/CONFIGURATION.md
 * @link https://arnaud.le-blanc.net/php-rdkafka-doc/phpdoc/class.rdkafka-kafkaconsumer.html
 */
class HighConsumer extends \Rdkafka\KafkaConsumer
{
    /**
     * @param Conf $conf
     */
    public function __construct($conf)
    {
        $conf->setOnRebalanceConf([Listeners\Rebalance::class]);
        parent::__construct($conf);
    }
}