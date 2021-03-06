<?php 

namespace Flysion\Kafka;

class ServiceProvider extends \Illuminate\Support\ServiceProvider
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

        require_once __DIR__ . '/helper.php';
    }
}