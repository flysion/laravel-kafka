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
<<<<<<< HEAD
        $config = array_merge(
            config("kafka.connections.{$name}", []),
            $config
        );
=======
        $config = array_merge(config("kafka.connections.{$name}", []), $config);

        if (is_null($config)) {
            throw new \InvalidArgumentException("Kafka producer [{$name}] is not defined.");
        }
>>>>>>> c2f6759aaf94ed44be7ea3b3b0d70bbcee660c7b

        return $this->create($config['config'], $config['topics'] ?? []);
    }

    /**
     * @param array $config
     * @param array $topicsConf
     * @return Producer
     */
    public function create($config, $topicsConf = [])
    {
        return new Producer(
            Conf::createFromArray($config),
            $topicsConf
        );
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