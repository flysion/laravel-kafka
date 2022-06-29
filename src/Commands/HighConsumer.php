<?php

namespace Flysion\Kafka\Commands;

class HighConsumer extends \Illuminate\Console\Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'kafka:HighConsumer {connection} {--consume-timeout=1000} {--topic=*} {--C|config=*}
                            {--logger= : 记录日志}
                            {--ignore-error : 忽略错误}
                            {--deserializer=raw : 数据格式解析}
                            {--processor=* : 消息处理方式，可选的值：null/file/job/event}
                            {--O|option=*}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '';

    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = '';

    /**
     * @var \Psr\Log\LoggerInterface
     */
    protected $logger;

    /**
     * @var \Flysion\Kafka\HighConsumer
     */
    protected $consumer;

    /**
     * Consumer message.
     */
    public function handle()
    {
        $consumer = app('kafka.highconsumer')->resolve($this->argument('connection'), $this->parseOptions($this->option("config")));
        $consumer->subscribe($this->option('topic'));

        $quit = false;

        pcntl_signal(SIGTERM/*15*/, function ($signal) use (&$quit, $consumer) {
            $this->logger()->info("quit", ['signal' => $signal]);
            $quit = true;
        });

        pcntl_signal(SIGINT/*2 or ctrl+c*/, function ($signal) use (&$quit, $consumer) {
            $this->logger()->info("quit", ['signal' => $signal]);
            $quit = true;
        });

        $ignoreError = $this->option('ignore-error');

        while (!$quit) {
            pcntl_signal_dispatch();

            $message = $consumer->consume(intval($this->option('consume-timeout')));

            try {
                $this->handleMessage($consumer, $message);
            } catch (\Throwable $e) {
                $this->error($e);
                $this->logger()->error($e, (array)$message);

                if (!$ignoreError) {
                    break;
                }
            }
        }

        $consumer->unsubscribe();
        $consumer->close();
    }

    /**
     * @param \Flysion\Kafka\Consumer $consumer
     * @param \Rdkafka\Message $message
     * @return boolean
     */
    protected function handleMessage($consumer, $message)
    {
        switch ($message->err) {
            case RD_KAFKA_RESP_ERR_NO_ERROR:
                $this->logger()->debug('[consumer]', (array)$message);
                $this->process($message);
                $consumer->commit($message);
                break;
            case RD_KAFKA_RESP_ERR__PARTITION_EOF:
                $this->logger()->info($message->errstr(), (array)$message);
                break;
            case RD_KAFKA_RESP_ERR__TIMED_OUT:
                $this->logger()->warning($message->errstr(), (array)$message);
                break;
            default:
                $this->logger()->error($message->errstr(), (array)$message);
                break;
        }

        return true;
    }

    /**
     * @return \Psr\Log\LoggerInterface
     */
    protected function logger()
    {
        if (!is_null($this->logger)) {
            return $this->logger;
        }

        if ($logger = $this->option('logger')) {
            $this->logger = \Illuminate\Support\Facades\Log::channel($logger);
        } else {
            $this->logger = app('log');
        }

        return $this->logger;
    }

    /**
     * @param \Rdkafka\Message $message
     */
    protected function process($message)
    {
        $processors = $this->option('processor');
        $options = $this->parseOptions($this->option('option'), true);
        $data = $this->deserialize($message->payload, $options, $message);

        foreach ($processors as $processor) {
            if (app()->has("processor.{$processor}")) {
                app("processor.{$processor}")->process($data, $options, $message, $this->argument('connection'));
            } elseif (is_callable($processor)) {
                call_user_func($processor, $data, $options, $message, $this->argument('connection'));
            } else {
                app($processor)->process($data, $options, $message, $this->argument('connection'));
            }
        }
    }

    /**
     * @param \Rdkafka\Message $message
     * @param array $options
     * @param string $payload
     * @return mixed
     */
    protected function deserialize($payload, $options, $message)
    {
        $deserializer = $this->option('deserializer', 'raw');

        if (app()->has("deserializer.{$deserializer}")) {
            return app("deserializer.{$deserializer}")->deserialize($payload, $options, $message, $this->argument('connection'));
        } elseif (is_callable($deserializer)) {
            return call_user_func($deserializer, $payload, $options, $message, $this->argument('connection'));
        } else {
            return app($deserializer)->deserialize($payload, $options, $message, $this->argument('connection'));
        }
    }

    /**
     * @param array $arguments
     * @param boolean $super
     * @return array
     */
    protected function parseOptions($arguments, $super = false)
    {
        $options = [];

        foreach ($arguments as $v) {
            if (strpos($v, '=') === false) {
                $key = $v;
                $value = true;
            } else {
                list($key, $value) = explode('=', $v, 2);
            }

            switch ($value) {
                case 'TRUE':
                    $value = true;
                    break;
                case 'FALSE':
                    $value = false;
                    break;
            }

            if ($super && substr($key, -2) == '[]') {
                $key = substr($key, 0, -2);
                if (!isset($options[$key])) {
                    $options[$key] = [];
                }

                $options[$key][] = $value;
            } else if ($super && strpos($key, '.') > 0) {
                \Illuminate\Support\Arr::set($options, $key, $value);
            } else {
                $options[$key] = $value;
            }
        }

        return $options;
    }
}
