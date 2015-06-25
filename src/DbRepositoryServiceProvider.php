<?php
namespace Wilgucki\DbRepository;

use Illuminate\Support\ServiceProvider;
use Wilgucki\DbRepository\Command\DbRepositoryTables;

/**
 * Package service provider
 *
 * @package wilgucki/dbrepository
 * @author Maciej Wilgucki <mwilgucki@gmail.com>
 * @copyright Maciej Wilgucki <mwilgucki@gmail.com>
 * @license https://github.com/wilgucki/dbrepository/blob/master/LICENSE
 * @link https://github.com/wilgucki/dbrepository
 */class DbRepositoryServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        $this->publishes([
            __DIR__ . '/../config/dbrepository.php' => config_path('dbrepository.php')
        ]);
    }

    /**
     * Register the application services.
     *
     * @return void
     */
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

