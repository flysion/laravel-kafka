<?php

namespace Flysion\Kafka\Commands;

use Illuminate\Support\Facades\Artisan;
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
                            {--data-decode=raw : 消费出来的数据的格式：raw-原值、json格式或其他closure}
                            {--stop-when-eof : 消费完毕退出}
                            {--handle=null : 消息处理方式，可选的值：null-什么都不做 file-写入文件 job-通过作业处理 event-转换成事件 callback-回调函数处理}
                            
                            {--file= : (handle=file)将消息写入文件}
                            
                            {--job= : (handle=job)将消息放到作业里边处理}
                            {--job-connection=sync : (handle=job)作业队列连接}
                            {--job-queue= : (handle=job)作业队列名称}
                            
                            {--events=events : (handle=event) 事件分发器名称 }
                            
                            {--callback= : (handle=callback) 回调函数}';

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
        if($logger = $this->option('logger')) {
            $this->logger = \Illuminate\Support\Facades\Log::channel($logger);
        } else {
            $this->logger = app('log');
        }

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

        $message->payload = $this->dataDecode($message->payload);

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

        $eventName = $this->argument('connection') .':'. $message->topic_name;

        $events->dispatch($eventName, [$this->argument('connection'), $eventName, $message->payload]);
    }

    /**
     * @param \Rdkafka\Message $message
     */
    protected function handleMessageToCallback($message)
    {
        call_user_func_array($this->option('callback'), [ $message ]);
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
    protected function dataDecode($data)
    {
        $dataDecode = $this->option('data-decode');

        if(is_callable($dataDecode)) {
            return $dataDecode($data);
        }

        $method = 'dataDecode' . ucfirst(Str::camel($dataDecode));
        if(!method_exists($this, $method)) {
            throw new \InvalidArgumentException("Data fromat [{$dataDecode}] is not supported.");
        }

        return $this->{$method}($data);
    }

    /**
     * 数据格式化-原始数据
     * @param $data
     * @return mixed
     */
    protected function dataDecodeRaw($data)
    {
        return $data;
    }

    /**
     * 数据格式化-json
     * @param string $data
     * @return array|bool|float|int|null|object|string
     */
    protected function dataDecodeJson($data)
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