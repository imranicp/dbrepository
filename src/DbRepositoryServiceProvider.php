<?php
namespace Wilgucki\DbRepository;

use Illuminate\Support\ServiceProvider;
use Wilgucki\DbRepository\Command\DbRepositoryTables;

class DbRepositoryServiceProvider extends ServiceProvider
{
    public function boot()
    {
        $this->publishes([
            __DIR__ . '/../config/dbrepository.php' => config_path('dbrepository.php')
        ]);
    }

    public function register()
    {
        $this->app['command.dbrepository.createtables'] = $this->app->share(
            function($app) {
                return new DbRepositoryTables();
            }
        );

        $this->commands('command.dbrepository.createtables');
    }
}