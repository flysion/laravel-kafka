<?php

namespace Flysion\Kafka;

class ConsumerManager
{
    /**
     * @var Consumer[]
     */
    protected $consumers = [];

    /**
     * @param string $name
     * @return Consumer
     */
    public function connection($name)
    {
        return $this->consumers[$name] = $this->get($name);
    }

    /**
     * @param string $name
     * @return Consumer
     */
    public function get($name)
    {
        return $this->consumers[$name] ?? $this->resolve($name);
    }

    /**
     * @param string $name
     * @param array $extraConfig
     * @return Consumer
     */
    public function resolve($name, $extraConfig = [])
    {
        $config = array_merge(config("kafka.connections.{$name}.config", []), $extraConfig);

        if (is_null($config)) {
            throw new \InvalidArgumentException("Kafka consumer [{$name}] is not defined.");
        }

        return $this->create($name, $config);
    }

    /**
     * @param string $name
     * @param array $config
     * @return Consumer
     */
    public function create($name, $config)
    {
        return new Consumer(
            $name,
            Conf::createFromArray($config)
        );
    }

    /**
     * @return Consumer
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