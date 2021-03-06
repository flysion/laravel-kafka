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

        $this->publishes([
            __DIR__ . '/../config/kafka.php' => $this->app->configPath('kafka.php')
        ], 'kafka');

        require_once __DIR__ . '/helper.php';
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