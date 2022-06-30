<?php

namespace Flysion\Kafka;

/**
 * @link http://kafka.apache.org/documentation/
 * @link https://github.com/arnaud-lb/php-rdkafka
 * @link https://github.com/edenhill/librdkafka/blob/master/CONFIGURATION.md
 * @link https://arnaud.le-blanc.net/php-rdkafka-doc/phpdoc/class.rdkafka-conf.html
 */
class Conf extends \Rdkafka\Conf
{
    /**
     *
     */
    public function __construct()
    {
        parent::__construct();

        $this->listenError();
        $this->listenDrMsg();
        $this->listenStats();
        $this->listenLog();
        $this->listenRebalance();

        app('events')->listen(Events\Rebalance::class, Listeners\Rebalance::class);
    }

    /**
     * @param string $key
     * @param mixed $value
     * @return $this
     */
    public function set($key, $value)
    {
        $method = 'set' . ucfirst(configname2camel($key)) . 'Conf';

        if(method_exists($this, $method)) {
            $this->{$method}($value);
        } else {
            parent::set($key, $value);
        }

        return $this;
    }

    /**
     * 绑定 error 回调，并触发事件
     */
    public function listenError()
    {
        $this->setErrorCb(function($kafka, $err, $reason) {
            $event = new Events\Error();
            $event->kafka = $kafka;
            $event->err = $err;
            $event->reason = $reason;

            app('events')->dispatch(Events\Error::class, $event);
        });
    }

    /**
     * 绑定 drMsg 回调，并触发事件
     */
    public function listenDrMsg()
    {
        $this->setDrMsgCb(function($kafka, $message) {
            $event = new Events\DrMsg();
            $event->kafka = $kafka;
            $event->message = $message;

            app('events')->dispatch(Events\DrMsg::class, $event);
        });
    }

    /**
     * 绑定 stats 回调，并触发事件
     */
    public function listenStats()
    {
        $this->setStatsCb(function($kafka, $json, $len) {
            $event = new Events\Stats();
            $event->kafka = $kafka;
            $event->stats = \GuzzleHttp\Utils::jsonDecode($json, true);

            app('events')->dispatch(Events\Stats::class, $event);
        });
    }

    /**
     * 绑定 log 回调，并触发事件
     */
    public function listenLog()
    {
        $this->setLogCb(function($kafka, $level, $facility, $message) {
            $event = new Events\Log();
            $event->kafka = $kafka;
            $event->level = $level;
            $event->facility = $facility;
            $event->message = $message;

            app('events')->dispatch(Events\Log::class, $event);
        });
    }

    /**
     * 绑定 rebalance 回调，并触发事件
     */
    public function listenRebalance()
    {
        $this->setRebalanceCb(function($kafka, $err, $partitions = null) {
            $event = new Events\Rebalance();
            $event->kafka = $kafka;
            $event->err = $err;
            $event->partitions = $partitions;

            app('events')->dispatch(Events\Rebalance::class, $event);
        });
    }

    /**
     * @param array $config
     * @return Conf
     */
    public static function createFromArray($config)
    {
        $instance = new static();

        foreach($config as $key => $value)
        {
            $instance->set($key, $value);
        }

        return $instance;
    }
}
