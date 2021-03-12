<?php

namespace Flysion\Kafka\Commands;

use Illuminate\Support\Str;

class HighConsumer extends \Illuminate\Console\Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'kafka:HighConsumer {connection} {--consume-timeout=1000} {--topic=*} {--C|config=*}
                            {--logger= : 记录日志}
                            {--data-format=raw : 消费出来的数据的格式：raw-原值 json}
                            {--stop-when-eof : 消费完毕退出}
                            {--handle=null : 消息处理方式，可选的值：null-什么都不做 file-写入文件 job-转到作业处理 event-转到事件 callback-转到自定义处理方法}
                            
                            {--file= : 将消息写入文件}
                            
                            {--job= : 将消息放到作业里边处理}
                            {--job-connection=sync : 作业队列连接}
                            {--job-queue= : 作业队列名称}
                            
                            {--events=events}
                            
                            {--callback=}';

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
        $this->logger = $this->hasOption('logger') ? \Illuminate\Support\Facades\Log::channel($this->option('logger')) : app('log');

        $this->consumer = app('kafka.highconsumer')->resolve($this->argument('connection'), $this->config());
        $this->consumer->subscribe($this->option('topic'));

        $quit = false;

        pcntl_signal(SIGTERM/*15*/, function($sig) use(&$quit) {
            $quit = true;
        });

        pcntl_signal(SIGINT/*2 or ctrl+c*/, function($sig) use(&$quit) {
            $quit = true;
        });

        while (!$quit) {
            pcntl_signal_dispatch();

            $message = $this->consumer->consume(intval($this->option('consume-timeout')));

            switch ($message->err) {
                case RD_KAFKA_RESP_ERR_NO_ERROR:
                    $this->logger->debug('consume.', (array)$message);
                    $this->handleMessage($message);
                    $this->consumer->commit();
                    break;
                case RD_KAFKA_RESP_ERR__PARTITION_EOF:
                    $this->logger->info($message->errstr(), (array)$message);
                    if($this->option('stop-when-eof')) break(2);
                    break;
                case RD_KAFKA_RESP_ERR__TIMED_OUT:
                    $this->logger->warning($message->errstr(), (array)$message);
                    break;
                default:
                    $this->logger->warning($message->errstr(), (array)$message);
                    break;
            }
        }
    }

    /**
     * @param \Rdkafka\Message $message
     */
    protected function handleMessage($message)
    {
        $handle = $this->option('handle');
        $method = 'handleMessageTo' . ucfirst(Str::camel($handle));

        if(!method_exists($this, $method)) {
            throw new \InvalidArgumentException("Handle [{$handle}] is not supported.");
        }

        $message->payload = $this->dataFormat($message->payload);
        $this->{$method}($message);
    }

    /**
     * @param \Rdkafka\Message $message
     */
    protected function handleMessageToFile($message)
    {
        file_put_contents($this->option('file'), \GuzzleHttp\Utils::jsonEncode($message) . "\n");
    }

    /**
     * @param \Rdkafka\Message $message
     */
    protected function handleMessageToJob($message)
    {
        $class = $this->option('job');
        $job = $class::dispatch($message);
        $job->onConnection($this->option('job-connection'));
        $job->onQueue($this->option('job-queue'));
    }

    /**
     * @param \Rdkafka\Message $message
     */
    protected function handleMessageToEvent($message)
    {
        $events = $this->option('events');

        if(!($events instanceof \Illuminate\Events\Dispatcher)) {
            $events = app($events);
        }

        $events->dispatch($this->argument('connection') .':'. $message->topic_name, [$message->topic_name, $message->payload]);
    }

    /**
     * @param \Rdkafka\Message $message
     */
    protected function handleMessageToCallback($message)
    {
        $callback = $this->option('callback', null);
        if($callback) {
            $callback($message);
        }
    }

    /**
     * @param \Rdkafka\Message $message
     */
    protected function handleMessageToNull($message)
    {
        // pass
    }

    /**
     * @param $data
     * @return mixed
     */
    protected function dataFormat($data)
    {
        $dataFormat = $this->option('data-format');
        $method = 'dataFormat' . ucfirst(Str::camel($dataFormat));

        if(!method_exists($this, $method)) {
            throw new \InvalidArgumentException("Data fromat [{$dataFormat}] is not supported.");
        }

        return $this->{$method}($data);
    }

    /**
     * 数据格式化-原始数据
     * @param $data
     * @return mixed
     */
    protected function dataFormatRaw($data)
    {
        return $data;
    }

    /**
     * 数据格式化-json
     * @param string $data
     * @return array|bool|float|int|null|object|string
     */
    protected function dataFormatJson($data)
    {
        return \GuzzleHttp\Utils::jsonDecode($data, true);
    }

    /**
     * @return array
     */
    protected function config()
    {
        $data = [];

        foreach ($this->option('config') as $v)
        {
            if(strpos($v, '=') === false) {
                $key = $v;
                $value = true;
            } else {
                list($key, $value) = explode('=', $v);
            }

            switch($value) {
                case 'TRUE':
                    $value = true;
                    break;
                case 'FALSE':
                    $value = false;
                    break;
            }

            if($key[0] === '*') {
                isset($data[$key]) ? ($data[$key][] = $value) : ($data[$key] = [$value]);
            } else {
                $data[$key] = $value;
            }
        }

        return $data;
    }
}