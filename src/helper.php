<?php

namespace Flysion\Kafka;

/**
 * 将 rdkafka 配置名称转换成驼峰命名
 *
 * @param string $str
 */
function configname2camel($str)
{
    return \Illuminate\Support\Str::camel(str_replace('.', '_', $str));
}

/**
 * @param $name
 * @return \App\Services\Kafka\Producer
 */
function kafka_producer($name)
{
    return app('kafka')->producer($name);
}

/**
 * @param $name
 * @return \Rdkafka\ProducerTopic
 */
function kafka_producer_topic($name)
{
    list($producer, $topic) = explode('.', $name, 2);
    return kafka_producer($producer)->topic($topic);
}