<?php
namespace LaraRepo\Criteria\Select;

use LaraRepo\Contracts\RepositoryInterface;
use LaraRepo\Criteria\Criteria;

class SelectRelationCriteria extends Criteria
{
    /**
     * @var array
     */
    private $relation;

    /**
     * @var
     */
    private $columns;

    /**
     * @var
     */
    private $prefix;

    /**
     * @param $relation
     * @param $columns
     * @param bool|false $prefix
     */
    public function __construct($relation, $columns, $prefix = true)
    {
        if (!is_array($columns)) {
            $columns = [
                $columns
            ];
        }

        $this->relation = $relation;
        $this->columns = $columns;
        $this->prefix = $prefix;
    }

    /**
     * @param $modelQuery
     * @param RepositoryInterface $repository
     * @return mixed
     * @throws \Exception
     */
    public function apply($modelQuery, RepositoryInterface $repository)
    {
        $relations = $repository->getRelations();

        if (!isset($relations[$this->relation])) {
            throw new \Exception(sprintf('%s is not associated with %s', $repository->getTable(), $this->relation));
        }

        $related = $relations[$this->relation]->getRelated();
        $table = $related->getTable();

        return $modelQuery->addSelect($repository->fixColumns($this->columns, $table, $this->prefix));
    }

}
