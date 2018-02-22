<?php

namespace LaraRepo\Criteria\Where;

use LaraRepo\Contracts\RepositoryInterface;
use LaraRepo\Criteria\Criteria;
use LaraRepo\Criteria\Traits\RelationTraitCriteria;

class WhereHasRelationCriteria extends Criteria
{
    use RelationTraitCriteria;
    // @ TODO move class in LaraRepo
    /**
     * @var
     */
    protected $relation;

    /**
     * @var
     */
    protected $where;

    /**
     * WhereHasRelationCriteria constructor.
     * @param $relation
     * @param $where
     */
    public function __construct($relation, $where = [])
    {
        $this->where = $where;
        $this->relation = $relation;
    }

    /**
     * @param $modelQuery
     * @param RepositoryInterface $repository
     * @return mixed
     */
    public function apply($modelQuery, RepositoryInterface $repository)
    {
        $table = $this->getRelationTable($repository);
        $modelQuery->whereHas($this->relation, function ($query) use($repository, $table) {
            foreach ($this->where as $column => $values) {
                if (!is_array($values)) {
                    $values = [
                        $values
                    ];
                }
                $query->select($repository->fixColumns($column, $table))
                      ->wherein($column, $values);
            }
        });
        return $modelQuery;
    }
}
