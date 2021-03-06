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

        $this->app->singleton('kafka', KafkaManager::class);

        require_once __DIR__ . '/helper.php';
    }
}