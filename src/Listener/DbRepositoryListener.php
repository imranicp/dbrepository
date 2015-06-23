<?php
namespace Wilgucki\DbRepository\Listener;

use App\Events;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

/**
 * Event listener that captures saving event trigerred in observed model.
 *
 * @package wilgucki/dbrepository
 * @author Maciej Wilgucki <mwilgucki@gmail.com>
 * @copyright Maciej Wilgucki <mwilgucki@gmail.com>
 * @license https://github.com/wilgucki/dbrepository/blob/master/LICENSE
 * @link https://github.com/wilgucki/dbrepository
 */
class DbRepositoryListener
{
    public function subscribe($events)
    {
        if(config('dbrepository.disabled') === true) return;

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

    /**
     * Handle saving event
     *
     * @param Model $model
     */
    public function onModelSaving(Model $model)
    {
        $table = $model->getTable();
        $attributes = $model->getAttributes();

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
