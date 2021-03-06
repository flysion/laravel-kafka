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
     * @var
     */
    private $dispatcher;

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
     * @return \Illuminate\Events\Dispatcher
     */
    protected function createDispatcher()
    {
        return (new \Illuminate\Events\Dispatcher())->setQueueResolver(function () {
            return app()->make(\Illuminate\Contracts\Queue\Factory::class);
        });
    }

    /**
     * @return \Illuminate\Events\Dispatcher
     */
    protected function dispatcher()
    {
        return $this->dispatcher = $this->dispatcher ?? $this->createDispatcher();
    }

    /**
     * @param array $listeners
     */
    public function setOnErrorConf($listeners)
    {
        $this->setErrorCb(function($kafka, $err, $reason) {
            $event = new Events\Error();
            $event->kafka = $kafka;
            $event->err = $err;
            $event->reason = $reason;
            $this->dispatcher()->dispatch(Events\Error::class, $event);
        });

        foreach($listeners as $listener) {
            $this->dispatcher()->listen(Events\Error::class, $listener);
        }
    }

    /**
     * @param array $listeners
     */
    public function setOnDrMsgConf($listeners)
    {
        $this->setDrMsgCb(function($kafka, $message) {
            $event = new Events\DrMsg();
            $event->kafka = $kafka;
            $event->message = $message;
            $this->dispatcher()->dispatch(Events\DrMsg::class, $event);
        });

        foreach($listeners as $listener) {
            $this->dispatcher()->listen(Events\DrMsg::class, $listener);
        }
    }

    /**
     * @param array $listeners
     */
    public function setOnStatsConf($listeners)
    {
        $this->setStatsCb(function($kafka, $json, $len) {
            $event = new Events\Stats();
            $event->kafka = $kafka;
            $event->stats = \GuzzleHttp\Utils::jsonDecode($json, true);
            $this->dispatcher()->dispatch(Events\Stats::class, $event);

        });

        foreach($listeners as $listener) {
            $this->dispatcher()->listen(Events\Stats::class, $listener);
        }
    }

    /**
     * @param array $listeners
     */
    public function setOnLogConf($listeners)
    {
        $this->setLogCb(function($kafka, $level, $facility, $message) {
            $event = new Events\Log();
            $event->kafka = $kafka;
            $event->level = $level;
            $event->facility = $facility;
            $event->message = $message;
            $this->dispatcher()->dispatch(Events\Log::class, $event);

        });

        foreach($listeners as $listener) {
            $this->dispatcher()->listen(Events\Log::class, $listener);
        }
    }

    /**
     * @param array $listeners
     */
    public function setOnRebalanceConf($listeners)
    {
        $this->setRebalanceCb(function($kafka, $err, $partitions = null) {
            $event = new Events\Rebalance();
            $event->kafka = $kafka;
            $event->err = $err;
            $event->partitions = $partitions;
            $this->dispatcher()->dispatch(Events\Rebalance::class, $event);
        });

        foreach($listeners as $listener) {
            $this->dispatcher()->listen(Events\Rebalance::class, $listener);
        }
    }

    /**
     * @param array $config
     * @return Conf
     */
    public function setMany($config)
    {
        foreach($config as $key => $value)
        {
            $this->set($key, $value);
        }

        return $this;
    }

    /**
     * @param array $config
     * @return Conf
     */
    public static function createFromArray($config)
    {
        $instance = new static();
        $instance->setMany($config);
        return $instance;
    }
}