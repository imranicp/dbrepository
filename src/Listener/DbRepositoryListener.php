<?php

namespace Wilgucki\DbRepository\Listener;

use App\Events;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

/**
 * Event listener that captures created, updated and deleted event trigerred in observed models.
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
        if (\Config::get('dbrepository.disabled') === true) {
            return false;
        }

        $listen = \Config::get('dbrepository.listen');

        if (is_array($listen)) {
            foreach ($listen as $class) {
                $events->listen(
                    'eloquent.created: '.$class,
                    'Wilgucki\DbRepository\Listener\DbRepositoryListener@onModelCreated'
                );

                $events->listen(
                    'eloquent.updated: '.$class,
                    'Wilgucki\DbRepository\Listener\DbRepositoryListener@onModelUpdated'
                );

                $events->listen(
                    'eloquent.deleting: '.$class,
                    'Wilgucki\DbRepository\Listener\DbRepositoryListener@onModelDeleted'
                );
            }
        }

        return true;
    }

    /**
     * Handle created event
     *
     * @param Model $model
     */
    public function onModelCreated(Model $model)
    {
        $this->save($model, 'created');
    }

    /**
     * Handle updated event
     *
     * @param Model $model
     */
    public function onModelUpdated(Model $model)
    {
        $this->save($model, 'updated');
    }

    /**
     * Handle deleted event
     *
     * @param Model $model
     */
    public function onModelDeleted(Model $model)
    {
        $this->save($model, 'deleted');
    }

    /**
     * Save data to repository table
     *
     * @param Model $model
     * @param string $changeType
     */
    private function save(Model $model, $changeType)
    {
        $rc = new \ReflectionClass($model);
        $class = $rc->getShortName();
        $namespace = $rc->getNamespaceName();

        $repositoryClass = $namespace.'\\Repository' . $class;

        $repository = new $repositoryClass;
        $repository->{str_singular($model->getTable()).'_id'} = $model->id;
        $repository->type = $changeType;
        $repository->data = $model->getAttributes();
        if (\Config::get('dbrepository.save_user') === true && \Auth::check()) {
            $repository->changed_by = \Auth::user()->id;
        }
        $repository->save();
    }
}
