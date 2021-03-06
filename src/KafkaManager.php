<?php

namespace Flysion\Kafka;

class KafkaManager
{
    /**
     * The array of resolved kafka connections.
     *
     * @var Producer[]
     */
    protected $producers = [];

    /**
     * @var HighConsumer[]
     */
    protected $kafkaConsumers = [];

    /**
     * @var Consumer[]
     */
    protected $consumer = [];

    /**
     * @param string $name
     * @return Producer
     */
    public function producer($name)
    {
        return $this->producers[$name] = $this->getProducer($name);
    }

    /**
     * @param string $name
     * @return Producer
     */
    public function getProducer($name)
    {
        return $this->producers[$name] ?? $this->resolveProducer($name);
    }

    /**
     * @param string $name
     * @param array $config
     * @return Producer
     */
    public function resolveProducer($name, $config = [])
    {
        $config = array_merge(config("kafka.connections.{$name}", []), $config);

        if (is_null($config)) {
            throw new \InvalidArgumentException("Kafka producer [{$name}] is not defined.");
        }

        return $this->createProducer($config['config'], $config['topics'] ?? []);
    }

    /**
     * @param array $config
     * @param array $topicsConf
     * @return Producer
     */
    public function createProducer($config, $topicsConf = [])
    {
        return new Producer(
            Conf::createFromArray($config),
            $topicsConf
        );
    }

    /**
     * @param string $name
     * @return HighConsumer
     */
    public function highConsumer($name)
    {
        return $this->highConsumer[$name] = $this->getHighConsumer($name);
    }

    /**
     * @param string $name
     * @return HighConsumer
     */
    public function getHighConsumer($name)
    {
        return $this->highConsumer[$name] ?? $this->resolveHighConsumer($name);
    }

    /**
     * @param string $name
     * @param array $config
     * @return HighConsumer
     */
    public function resolveHighConsumer($name, $config = [])
    {
        $config = array_merge(config("kafka.connections.{$name}.config", []), $config);

        if (is_null($config)) {
            throw new \InvalidArgumentException("Kafka consumer [{$name}] is not defined.");
        }

        return $this->createHighConsumer($config);
    }

    /**
     * @param array $config
     * @return HighConsumer
     */
    public function createHighConsumer($config)
    {
        return new HighConsumer(
            Conf::createFromArray($config)
        );
    }

    /**
     * @param string $name
     * @return Consumer
     */
    public function consumer($name)
    {
        return $this->consumer[$name] = $this->getConsumer($name);
    }

    /**
     * @param string $name
     * @return Consumer
     */
    public function getConsumer($name)
    {
        return $this->consumer[$name] ?? $this->resolveConsumer($name);
    }

    /**
     * @param string $name
     * @param array $config
     * @return Consumer
     */
    public function resolveConsumer($name, $config = [])
    {
        $config = array_merge(config("kafka.connections.{$name}.config", []), $config);

        if (is_null($config)) {
            throw new \InvalidArgumentException("Kafka consumer [{$name}] is not defined.");
        }

        return $this->createConsumer($config);
    }

    /**
     * @param array $config
     * @return Consumer
     */
    public function createConsumer($config)
    {
        return new Consumer(
            Conf::createFromArray($config)
        );
    }
}