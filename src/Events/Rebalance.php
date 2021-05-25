<?php

namespace Flysion\Kafka\Events;

class Rebalance
{
    /**
     * @var
     */
    public $kafka;

    /**
     * @var int
     */
    public $err;

    /**
     * @var
     */
    public $partitions;
}