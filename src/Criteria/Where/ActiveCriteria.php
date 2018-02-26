<?php

namespace LaraRepo\Criteria\Where;

use LaraRepo\Contracts\RepositoryInterface;
use LaraRepo\Criteria\Criteria;
use LaraSupport\Facades\LaraDB;

class ActiveCriteria extends Criteria
{
    /**
     * @var int
     */
    private $active = 1;

    /**
     * @param $modelQuery
     * @param RepositoryInterface $repository
     * @return mixed
     */
    public function apply($modelQuery, RepositoryInterface $repository)
    {
        $column = $repository->getStatusColumn();
        if ($column && LaraDB::hasColumn($repository->getTable(), $column)) {
            return $modelQuery->where(
                $repository->fixColumns($repository->getStatusColumn()), '=',
                $this->active);
        }

        return $modelQuery;
    }
}
