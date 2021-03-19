<?php

namespace Flysion\Kafka;

/**
 * @link http://kafka.apache.org/documentation/
 * @link https://github.com/arnaud-lb/php-rdkafka
 * @link https://github.com/edenhill/librdkafka/blob/master/CONFIGURATION.md
 * @link https://arnaud.le-blanc.net/php-rdkafka-doc/phpdoc/class.rdkafka-topicconf.html
 */
class TopicConf extends \Rdkafka\TopicConf
{
    /**
     * @param string $key
     * @param mixed $value
     * @return $this
     */
    public function set($key, $value)
    {
        $method = 'set' . ucfirst(configname2camel($key)) . 'Conf';

        if(method_exists($this, $method)) {
            $this->{$method}($value);
        } else {
            parent::set($key, $value);
        }

        return $this;
    }

    /**
     * @param int $value
     */
    public function setPartitionerConf($value)
    {
        parent::setPartitioner($value);
    }

    /**
     * @param array $config
     * @return TopicConf
     */
    public function setMany($config)
    {
        foreach($config as $key => $value)
        {
            $this->set($key, $value);
        }

        return $this;
    }

    /**
     * @param array $config
     * @return TopicConf
     */
    public static function createFromArray($config)
    {
        $instance = new static();
        $instance->setMany($config);
        return $instance;
    }
}