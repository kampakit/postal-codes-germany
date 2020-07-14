<?php
namespace Kampakit\PostalCodesGermany;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\ServiceProvider;
use Kampakit\PostalCodesGermany\Console\Commands\ImportCommand;
use Kampakit\PostalCodesGermany\Exceptions\DatabaseNotImplementedException;
use Kampakit\PostalCodesGermany\SearchInterface;

class PostalCodesGermanyServiceProvider extends ServiceProvider
{
    public function register(){
        $this->app->singleton(SearchInterface::class, function () {
            $db_type = DB::getDriverName();
            if ($db_type == 'pgsql') {
                return new SearchPostgres();
            } else {
                throw new DatabaseNotImplementedException(
                    'Databases other than PotsgreSQL are not yet implemented. Your DB_CONNECTION is not "pgsql"'
                );
            }
        });
    }

    public function boot() {
        if ($this->app->runningInConsole()) {
            $this->commands([
                ImportCommand::class
            ]);
        }
        $this->loadRoutesFrom(__DIR__.'/../routes/api.php');
        $this->loadMigrationsFrom(__DIR__.'/../database/migrations');
    }
}