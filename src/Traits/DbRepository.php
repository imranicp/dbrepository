<?php

namespace Wilgucki\DbRepository\Traits;

use Carbon\Carbon;

/**
 * Trait extending model class with additional methods for getting repository data for specific model id
 *
 * @package wilgucki/dbrepository
 * @author Maciej Wilgucki <mwilgucki@gmail.com>
 * @copyright Maciej Wilgucki <mwilgucki@gmail.com>
 * @license https://github.com/wilgucki/dbrepository/blob/master/LICENSE
 * @link https://github.com/wilgucki/dbrepository
 */
trait DbRepository
{
    protected $repositoryModelClass = null;

    /**
     * Returns all revisions for current row
     *
     * @return \Illuminate\Support\Collection
     */
    public function getRevisions()
    {
        return $this->getQuery()->orderBy('created_at')->get();
    }

    /**
     * Returns specific revision
     *
     * @param integer $revisionId
     * @return mixed
     */
    public function getRevision($revisionId)
    {
        return $this->getQuery()->where('id', $revisionId)->first();
    }

    /**
     * Returns last revision
     *
     * @return mixed
     */
    public function getLastRevision()
    {
        return $this->getQuery()->orderBy('created_at', 'desc')->limit(1)->first();
    }

    /**
     * Returns first revision
     *
     * @return mixed
     */
    public function getFirstRevision()
    {
        return $this->getQuery()->orderBy('created_at', 'asc')->limit(1)->first();
    }

    /**
     * Returns revisions to specified date
     *
     * @param string|Carbon $date
     * @return \Illuminate\Support\Collection
     */
    public function getRevisionsToDate($date)
    {
        return $this->getQuery()->where('created_at', '<=', $date)->orderBy('created_at')->get();
    }

    /**
     * Returns revisions from specified date
     *
     * @param string|Carbon $date
     * @return \Illuminate\Support\Collection
     */
    public function getRevisionsFromDate($date)
    {
        return $this->getQuery()->where('created_at', '>=', $date)->orderBy('created_at')->get();
    }

    /**
     * Returns repository model class name
     *
     * @return string
     */
    protected function getRepositoryModelClass()
    {
        if ($this->repositoryModelClass === null) {
            $reflection = new \ReflectionClass(__CLASS__);
            $this->repositoryModelClass = $reflection->getNamespaceName().'\\Repository'.$reflection->getShortName();
        }
        return $this->repositoryModelClass;
    }

    /**
     * Returns query builder object used by model methods to get repository data
     *
     * @return \Illuminate\Database\Query\Builder
     */
    protected function getQuery()
    {
        $class = $this->getRepositoryModelClass();
        return $class::where($this->getTable().'_id', $this->id);
    }
}
