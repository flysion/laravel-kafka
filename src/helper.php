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



/**
 * Wrapper for json_decode that throws when an error occurs.
 *
 * @param string $json    JSON data to parse
 * @param bool $assoc     When true, returned objects will be converted
 *                        into associative arrays.
 * @param int    $depth   User specified recursion depth.
 * @param int    $options Bitmask of JSON decode options.
 *
 * @return mixed
 * @throws Exception\InvalidArgumentException if the JSON cannot be decoded.
 * @link http://www.php.net/manual/en/function.json-decode.php
 */
function json_decode($json, $assoc = false, $depth = 512, $options = 0)
{
    $data = \json_decode($json, $assoc, $depth, $options);
    if (JSON_ERROR_NONE !== json_last_error()) {
        throw new \InvalidArgumentException(
            'json_decode error: ' . json_last_error_msg()
        );
    }

    return $data;
}

/**
 * Wrapper for JSON encoding that throws when an error occurs.
 *
 * @param mixed $value   The value being encoded
 * @param int    $options JSON encode option bitmask
 * @param int    $depth   Set the maximum depth. Must be greater than zero.
 *
 * @return string
 * @throws Exception\InvalidArgumentException if the JSON cannot be encoded.
 * @link http://www.php.net/manual/en/function.json-encode.php
 */
function json_encode($value, $options = 0, $depth = 512)
{
    $json = \json_encode($value, $options, $depth);
    if (JSON_ERROR_NONE !== json_last_error()) {
        throw new \InvalidArgumentException(
            'json_encode error: ' . json_last_error_msg()
        );
    }

    return $json;
}
