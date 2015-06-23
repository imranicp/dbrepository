<?php
namespace Wilgucki\DbRepository\Command;

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
        if(config('dbrepository.disabled') === true) return;

        $listen = config('dbrepository.listen');

        if(is_array($listen)) {
            foreach ($listen as $class) {
                $obj = new $class;
                $tableName = $obj->getTable();
                $columns = \Schema::getColumnListing($tableName);
                if (!\Schema::hasTable($tableName . '_repository')) {
                    \Schema::create($tableName . '_repository', function ($table) use ($columns, $tableName) {
                        $table->increments('id');
                        $table->timestamps();

                        foreach ($columns as $column) {
                            $columnName = $tableName . '_' . $column;
                            $table->text($columnName)->nullable();
                        }
                    });
                }
            }
        }
    }
}
