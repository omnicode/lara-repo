<?php

namespace LaraRepo\Criteria\Join;

use Illuminate\Database\Eloquent\RelationNotFoundException;
use LaraRepo\Contracts\RepositoryInterface;
use LaraRepo\Criteria\Criteria;

class InnerJoinCriteria extends Criteria
{
    /**
     * @var
     */
    protected $relation;

    /**
     * @var
     */
    protected $column;

    /**
     * @var array
     */
    protected $values;

    /**
     * @param $relation
     * @param string $column
     * @param array $values
     */
    public function __construct($relation, $column = '', $values = [])
    {
        if (!is_array($values)) {
            $values = [
                $values
            ];
        }

        $this->values = $values;
        $this->column = $column;
        $this->relation = $relation;
    }

    /**
     * @param $modelQuery
     * @param RepositoryInterface $repository
     * @return mixed
     */
    public function apply($modelQuery, RepositoryInterface $repository)
    {
        $relations = $repository->getRelations();

        if (!isset($relations[$this->relation])) {
            throw new RelationNotFoundException(sprintf('%s is not related to %s table', $repository->getTable(),
                $this->relation));
        }

        $related = $repository->getRelations()[$this->relation];
        $table = $related->getRelated()->getTable();
        $modelQuery->join($table, $repository->fixColumns('id'), '=', $related->getForeignKey());

        if ($this->column && $this->values) {
            $modelQuery->whereIn($repository->fixColumns($this->column, $table), $this->values);
        }

        return $modelQuery;
    }
}
