<?php

namespace Flysion\Kafka;

class HighConsumerManager
{

    /**
     * @var HighConsumer[]
     */
    protected $consumers = [];

    /**
     * @param string $name
     * @return HighConsumer
     */
    public function connection($name)
    {
        return $this->consumers[$name] = $this->get($name);
    }

    /**
     * @param string $name
     * @return HighConsumer
     */
    public function get($name)
    {
        return $this->consumers[$name] ?? $this->resolve($name);
    }

    /**
     * @param string $name
     * @param array $config
     * @return HighConsumer
     */
    public function resolve($name, $config = [])
    {
        $config = array_merge(config("kafka.connections.{$name}.config", []), $config);

        if (is_null($config)) {
            throw new \InvalidArgumentException("Kafka consumer [{$name}] is not defined.");
        }

        return $this->create($config);
    }

    /**
     * @param array $config
     * @return HighConsumer
     */
    public function create($config)
    {
        return new HighConsumer(
            Conf::createFromArray($config)
        );
    }

    /**
     * @return HighConsumer
     */
    public function default()
    {
        return $this->connection(config('kafka.default'));
    }

    /**
     * @param string $name
     * @param mixed[] $arguments
     * @return mixed
     */
    public function __call($name, $arguments)
    {
        return $this->default()->{$name}(...$arguments);
    }
}