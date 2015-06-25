<?php
namespace Wilgucki\DbRepository\Command;

use Illuminate\Support\Facades\Artisan;


/**
 * Artisan command for creating repository tables based on models listed in configuration class.
 *
 * @package wilgucki/dbrepository
 * @author Maciej Wilgucki <mwilgucki@gmail.com>
 * @copyright Maciej Wilgucki <mwilgucki@gmail.com>
 * @license https://github.com/wilgucki/dbrepository/blob/master/LICENSE
 * @link https://github.com/wilgucki/dbrepository
 */
class DbRepositoryTables extends \Illuminate\Console\Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'dbrepository:createtables';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create tables for observed tables.';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        if(\Config::get('dbrepository.disabled') === true) return;

        $listen = \Config::get('dbrepository.listen');

        if(is_array($listen)) {
            foreach ($listen as $class) {
                $obj = new $class;
                $tableName = $obj->getTable();
                $columns = \Schema::getColumnListing($tableName);

                if (!\Schema::hasTable('repository_' . $tableName)) {
                    \Schema::create('repository_' . $tableName, function ($table) use ($columns, $tableName) {
                        $table->increments('id');
                        $table->integer('changed_by')->nullable();
                        $table->text('type')->nullable();
                        $table->timestamps();

                        foreach ($columns as $column) {
                            $columnName = $tableName . '_' . $column;
                            $table->text($columnName)->nullable();
                        }
                    });

                    $rc = new \ReflectionClass($class);
                    $className = $rc->getShortName();

                    Artisan::call('make:model', ['name' => 'Repository' . $className]);
                }
            }
        }
    }
}

