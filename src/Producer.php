<?php

namespace Flysion\Kafka;

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
     */
    public function __construct($name, $conf)
    {
        parent::__construct($conf);
        $this->name = $name;
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
     * @param array $config
     * @return ProducerTopic
     */
    public function createTopic($name, array $config = [])
    {
        return $this->newTopic(
            $name,
            array_merge(config("{$this->name}.topics.{$name}", []), $config)
        );
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
            $this,
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

    /**
     * 在实例销毁之前将消息发送出去
     */
    public function __destruct()
    {
        $this->flush(-1);
    }
}