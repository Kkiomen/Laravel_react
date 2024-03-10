<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Database\Connection;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Events\QueryExecuted;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        // Repository
//        $this->app->bind(RepositorySqlInterface::class, FirebirdSqlRepository::class);

    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        DB::whenQueryingForLongerThan(500, function (Connection $connection, QueryExecuted $event) {
           $connection->rollBack();
           $connection->disconnect();
           throw new \Exception('Query took too long');
        });
    }
}
