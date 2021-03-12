<?php

namespace Flysion\Kafka;

use function GuzzleHttp\Promise\inspect;

/**
 * @link http://kafka.apache.org/documentation/
 * @link https://github.com/arnaud-lb/php-rdkafka
 * @link https://github.com/edenhill/librdkafka/blob/master/CONFIGURATION.md
 * @link https://arnaud.le-blanc.net/php-rdkafka-doc/phpdoc/class.rdkafka-producer.html
 */
class Producer extends \Rdkafka\Producer
{
    /**
     * @var array
     */
    protected $topicsConf;

    /**
     * @var array
     */
    private $topics;

    /**
     * @param Conf $conf
     * @param array $topicsConf
     */
    public function __construct($conf, $topicsConf = [])
    {
        parent::__construct($conf);
        $this->topicsConf = $topicsConf;
    }

    /**
     * @param $name
     * @return \RdKafka\ProducerTopic
     */
    public function topic($name)
    {
        return $this->topics[$name] = $this->topics[$name] ?? $this->createTopic($name);
    }

    /**
     * @param $name
     * @param int $partitioner
     * @return \RdKafka\ProducerTopic
     */
    public function createTopic($name)
    {
        return $this->newTopic($name, $this->topicsConf[$name] ?? null);
    }

    /**
     * @param string $name
     * @param array|TopicConf $conf
     * @return mixed
     */
    public function newTopic($name, $conf = null)
    {
        return parent::newTopic($name, $conf instanceof \Rdkafka\TopicConf || $conf instanceof TopicConf ? $conf : (is_null($conf) ? null : $this->createTopicConf($conf)));
    }

    /**
     * @param array $config
     * @return TopicConf
     */
    protected function createTopicConf($config)
    {
        return TopicConf::createFromArray($config);
    }
}