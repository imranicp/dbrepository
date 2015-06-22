<?php
namespace Wilgucki\DbRepository\Listener;

use App\Events;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class DbRepositoryListener
{
    public function subscribe($events)
    {

        $listen = config('dbrepository.listen');

        if(is_array($listen)) {
            foreach ($listen as $class) {
                $events->listen(
                    'eloquent.saving: ' . $class,
                    'Wilgucki\DbRepository\Listener\DbRepositoryListener@onModelSaving'
                );
            }
        }
    }

    public function onModelSaving($event)
    {
        $table = $event->getTable();
        $attributes = $event->getAttributes();

        $columns = [];
        $data = [];
        foreach($attributes as $name => $value) {
            $columns[$table . '_' . $name] = '?';
            $data[] = $value;
        }

        $sql  = 'insert into ' . $table . '_repository (' . implode(',', array_keys($columns)) . ') values (';
        $sql .= implode(',', $columns);
        $sql .= ')';

        \DB::insert($sql, $data);
    }
}
