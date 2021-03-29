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
     * @var string
     */
    public $name;

    /**
     * @param string $name
     * @param Conf $conf
     */
    public function __construct($name, $conf)
    {
        parent::__construct($conf);
        $this->name = $name;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }
}