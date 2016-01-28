<?php

namespace Wilgucki\DbRepository\Command;

use Illuminate\Support\Facades\Artisan;
use Illuminate\Console\Command;

/**
 * Artisan command for creating migrations and repository models based on models
 * listed in a configuration class.
 *
 * @package wilgucki/dbrepository
 * @author Maciej Wilgucki <mwilgucki@gmail.com>
 * @copyright Maciej Wilgucki <mwilgucki@gmail.com>
 * @license https://github.com/wilgucki/dbrepository/blob/master/LICENSE
 * @link https://github.com/wilgucki/dbrepository
 */
class DbRepositoryGenerator extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'dbrepository:generate';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create models and migrations for observed tables.';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        if (\Config::get('dbrepository.disabled') === true) {
            return;
        }

        $listen = \Config::get('dbrepository.listen');

        if (is_array($listen)) {
            foreach ($listen as $class) {
                $tableName = (new $class)->getTable();

                if (!\Schema::hasTable('repository_' . $tableName)) {
                    $this->createMigrationFile($tableName);
                    $this->createModel($class);
                }
            }
        }
    }

    /**
     * Create migration file
     *
     * @param string $tableName
     */
    protected function createMigrationFile($tableName)
    {
        $stubPath = realpath(__DIR__.'/../../stubs');
        $stub = file_get_contents($stubPath.'/migration.stub');
        $stub = str_replace(
            ['RepositoryClass', 'RepositoryTable', 'ForeignKey', 'ForeignTable'],
            [
                'CreateRepository'.ucfirst(camel_case($tableName)).'Table',
                'repository_'.$tableName,
                str_singular($tableName).'_id',
                $tableName
            ],
            $stub
        );

        $migrationName = date('Y_m_d_His').'_create_repository_'.$tableName.'_table';
        file_put_contents(database_path('migrations/'.$migrationName.'.php'), $stub);
    }

    /**
     * Use artisian command to generate repository model
     *
     * @param string $class
     */
    protected function createModel($class)
    {
        $rc = new \ReflectionClass($class);
        $className = $rc->getShortName();

        $stubPath = realpath(__DIR__.'/../../stubs');
        $stub = file_get_contents($stubPath.'/model.stub');
        $stub = str_replace(['ModelClass'], ['Repository'.$className], $stub);

        file_put_contents(app_path('Repository'.$className.'.php'), $stub);
    }
}
