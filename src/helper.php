<?php

namespace Flysion\Kafka;

/**
 * 将 rdkafka 配置名称转换成驼峰命名
 *
 * @param string $str
 * @return string
 */
function configname2camel($str)
{
    return \Illuminate\Support\Str::camel(str_replace('.', '_', $str));
}

/**
 * @param string|null $name
 * @return \Flysion\Kafka\Producer|\Flysion\Kafka\ProducerManager
 */
function kafka_producer($name = null)
{
    return is_null($name) ? app('kafka.producer') : app('kafka.producer')->connection($name);
}

/**
 * @param $name
 * @return \Flysion\Kafka\ProducerTopic
 */
function kafka_producer_topic($name)
{
    list($producer, $topic) = explode('.', $name, 2);
    return kafka_producer($producer)->topic($topic);
}

/**
 * @param string|null $name
 * @return \Flysion\Kafka\HighConsumer|\Flysion\Kafka\HighConsumerManager
 */
function kafka_high_consumer($name = null)
{
    return is_null($name) ? app('kafka.highconsumer') : app('kafka.highconsumer')->connection($name);
}

/**
 * @param string|null $name
 * @return \Flysion\Kafka\Consumer|\Flysion\Kafka\ConsumerManager
 */
function kafka_consumer($name = null)
{
    return is_null($name) ? app('kafka.consumer') : app('kafka.consumer')->connection($name);
}