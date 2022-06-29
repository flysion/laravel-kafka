<?php

namespace Flysion\Kafka;

use Illuminate\Contracts\Support\DeferrableProvider;

class ServiceProvider extends \Illuminate\Support\ServiceProvider implements DeferrableProvider
{
    protected $commands = [
        Commands\HighConsumer::class
    ];

    /**
     * Bootstrap any application services.
     * @return void
     */
    public function boot()
    {

    }

    /**
     * Register any application services.
     * @return void
     */
    public function register()
    {
        $this->commands($this->commands);

        $this->app->singleton('kafka.consumer', ConsumerManager::class);
        $this->app->singleton('kafka.highconsumer', HighConsumerManager::class);
        $this->app->singleton('kafka.producer', ProducerManager::class);

        $this->app->singleton(Deserializer\Raw::class);
        $this->app->alias(Deserializer\Raw::class, 'deserializer.raw');
        $this->app->singleton(Deserializer\Json::class);
        $this->app->alias(Deserializer\Json::class, 'deserializer.json');

        $this->app->singleton(Processor\Event::class);
        $this->app->alias(Processor\Event::class, 'processor.event');
        $this->app->singleton(Processor\File::class);
        $this->app->alias(Processor\File::class, 'processor.file');
        $this->app->singleton(Processor\Job::class);
        $this->app->alias(Processor\Job::class, 'processor.job');
        $this->app->singleton(Processor\Null::class);
        $this->app->alias(Processor\Null::class, 'processor.null');

        $this->publishes([
            __DIR__ . '/../config/kafka.php' => $this->app->configPath('kafka.php')
        ], 'kafka');
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return [
            'kafka.consumer',
            'kafka.highconsumer',
            'kafka.producer'
        ];
    }
}
