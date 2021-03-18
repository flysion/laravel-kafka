<?php

namespace Flysion\Kafka\Listeners;

class Rebalance
{
    public function handle(\Flysion\Kafka\Events\Rebalance $event)
    {
        switch ($event->err) {
            case RD_KAFKA_RESP_ERR__ASSIGN_PARTITIONS:
                $event->kafka->assign($event->partitions);
                dump($event->partitions);
                break;
            case RD_KAFKA_RESP_ERR__REVOKE_PARTITIONS:
                $event->kafka->assign(NULL);
                break;
            default:
                throw new \Exception($event->err);
        }
    }
}