<?php

namespace Flysion\Kafka\Events;

class Log
{
    /**
     * @var
     */
    public $kafka;

    /**
     * @var int
     */
    public $level;

    /**
     * @var string
     */
    public $facility;

    /**
     * @var string
     */
    public $message;

    /**
     * @var array
     */
    public static $laravelLevel = [
        LOG_EMERG => 'emergency',
        LOG_ALERT => 'alert',
        LOG_CRIT => 'critical',
        LOG_ERR => 'error',
        LOG_WARNING => 'warning',
        LOG_NOTICE => 'notice',
        LOG_INFO => 'info',
        LOG_DEBUG => 'debug'
    ];
}