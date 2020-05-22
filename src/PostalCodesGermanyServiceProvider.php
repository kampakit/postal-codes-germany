<?php
namespace Kampakit\PostalCodesGermany;

use Illuminate\Support\ServiceProvider;
use Kampakit\PostalCodesGermany\Console\Commands\ImportCommand;

class PostalCodesGermanyServiceProvider extends ServiceProvider
{
    public function register(){

    }

    public function boot() {
        if ($this->app->runningInConsole()) {
            $this->commands([
                ImportCommand::class
            ]);
        }
        $this->publishes([__DIR__.'/config/postal-codes-germany.php']);
//        $this->loadRoutesFrom(__DIR__.'/../routes/api.php');
//        $this->loadMigrationsFrom(__DIR__.'/../database/migrations');
    }
}