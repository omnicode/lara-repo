<?php
namespace LaraRepo\Criteria\Group;

use LaraRepo\Contracts\RepositoryInterface;
use LaraRepo\Criteria\Criteria;

class GroupByCriteria extends Criteria
{
    /**
     * @var
     */
    private $columns;

    /**
     * @param $columns
     */
    public function __construct($columns)
    {
        if (!is_array($columns)) {
            $columns = [
                $columns
            ];
        }

        $this->columns = $columns;
    }

    /**
     * @param $modelQuery
     * @param RepositoryInterface $repository
     * @return mixed
     */
    public function apply($modelQuery, RepositoryInterface $repository)
    {
        foreach ($this->columns as $column) {
            $modelQuery->groupBy($repository->fixColumns($column));
        }

        return $modelQuery;
    }

}
