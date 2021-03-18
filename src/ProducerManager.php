<?php

namespace Flysion\Kafka;

class ProducerManager
{
    /**
     * The array of resolved kafka connections.
     *
     * @var Producer[]
     */
    protected $producers = [];

    /**
     * @param string $name
     * @return Producer
     */
    public function connection($name)
    {
        return $this->producers[$name] = $this->get($name);
    }

    /**
     * @param string $name
     * @return Producer
     */
    public function get($name)
    {
        return $this->producers[$name] ?? $this->resolve($name);
    }

    /**
     * @param string $name
     * @param array $config
     * @return Producer
     */
    public function resolve($name, $config = [])
    {
        $config = array_merge(
            config("kafka.connections.{$name}", []),
            $config
        );

        return $this->create($name, $config['config']);
    }

    /**
     * @param string $name
     * @param array $config
     * @return Producer
     */
    public function create($name, $config)
    {
        return new Producer($name, Conf::createFromArray($config));
    }

    /**
     * @return Producer
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
