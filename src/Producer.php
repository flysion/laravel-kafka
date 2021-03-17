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
     * @var string
     */
    public $name;

    /**
     * @var array
     */
    protected $topicsConf;

    /**
     * @var array
     */
    private $topics;

    /**
     * @param string $name
     * @param Conf $conf
     * @param array $topicsConf
     */
    public function __construct($name, $conf, $topicsConf = [])
    {
        parent::__construct($conf);
        $this->topicsConf = $topicsConf;
        $this->name = $name;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param $name
     * @return ProducerTopic
     */
    public function topic($name)
    {
        return $this->topics[$name] = $this->topics[$name] ?? $this->createTopic($name);
    }

    /**
     * @param $name
     * @param int $partitioner
     * @return ProducerTopic
     */
    public function createTopic($name)
    {
        return $this->newTopic($name, $this->topicsConf[$name] ?? null);
    }

    /**
     * @param string $name
     * @param array|TopicConf $conf
     * @return ProducerTopic
     */
    public function newTopic($name, $conf = null)
    {
        $conf = $conf instanceof \Rdkafka\TopicConf || $conf instanceof TopicConf ? $conf : (is_null($conf) ? null : $this->createTopicConf($conf));

        return new ProducerTopic(
            $this->getName(),
            parent::newTopic($name, $conf)
        );
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