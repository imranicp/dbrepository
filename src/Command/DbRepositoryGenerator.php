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
                $sm = \DB::getDoctrineSchemaManager();
                $columns = $sm->listTableColumns($tableName);

                if (!\Schema::hasTable('repository_' . $tableName)) {
                    $this->createMigrationFile($columns, $tableName);
                    $this->createModel($class);
                }
            }
        }
    }

    /**
     * Create database columns for migration file
     *
     * @param string $columns
     * @param string $tableName
     * @return string
     */
    protected function createColumns($columns, $tableName)
    {
        $out = '';
        foreach ($columns as $columnName => $columnMeta) {
            $name = $tableName.'_'.$columnName;
            $type = camel_case(
                (string)$columnMeta->getType()
            );

            if ($type == 'string') {
                $length = $columnMeta->getLength();
                if ($length === null) {
                    $length = 255;
                }
                $out .= str_repeat(' ', 12)
                    ."\$table->{$type}('{$name}', {$length})->nullable();".PHP_EOL;
            } else {
                $out .= str_repeat(' ', 12)
                    ."\$table->{$type}('{$name}')->nullable();".PHP_EOL;
            }
        }
        return $out;
    }

    /**
     * Create migration file
     *
     * @param string $columns
     * @param string $tableName
     */
    protected function createMigrationFile($columns, $tableName)
    {
        $columnsTxt = $this->createColumns($columns, $tableName);
        $stubPath = realpath(__DIR__.'/../../stubs');
        $stub = file_get_contents($stubPath.'/migration.stub');
        $stub = str_replace(
            ['RepositoryClass', 'RepositoryTable', 'RepositoryColumns'],
            ['CreateRepository'.ucfirst(camel_case($tableName)).'Table', 'repository_' . $tableName, $columnsTxt],
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

        Artisan::call(
            'make:model',
            ['name' => 'Repository' . $className]
        );
    }
}
