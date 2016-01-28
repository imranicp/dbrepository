<?php

namespace Wilgucki\DbRepository\Traits;

use Carbon\Carbon;
use Diff\Differ\ListDiffer;
use Diff\Differ\MapDiffer;

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

    public function revisions()
    {
        return $this->hasMany($this->getRepositoryModelClass());
    }

    /**
     * Compares two revisions
     *
     * @param int $revision_id_from
     * @param int $revision_id_to
     *
     * @return array Key is the name of the column with changes. Value is an array
     *               containing change type as well as new (revision to) and old (revision from) value.
     */
    public function compareRevisions($revision_id_from, $revision_id_to)
    {
        $revision_from = $this->getRevision($revision_id_from);
        $revision_to = $this->getRevision($revision_id_to);

        $differ = new MapDiffer();
        $diff = $differ->doDiff($revision_from->data, $revision_to->data);
        $out = [];
        foreach ($diff as $k => $v) {
            if ($k == 'created_at' || $k == 'updated_at') {
                continue;
            }
            $out[$k] = $v->toArray();
        }
        return $out;
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
        return $this->getQuery()->orderBy('created_at', 'desc')->first();
    }

    /**
     * Returns first revision
     *
     * @return mixed
     */
    public function getFirstRevision()
    {
        return $this->getQuery()->orderBy('created_at', 'asc')->first();
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
        return $class::where(str_singular($this->getTable()).'_id', $this->id);
    }
}
