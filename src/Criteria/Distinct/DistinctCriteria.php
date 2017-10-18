<?php
namespace LaraRepo\Criteria\Distinct;

use LaraRepo\Contracts\RepositoryInterface;
use LaraRepo\Criteria\Criteria;

class DistinctCriteria extends Criteria
{
    /**
     * @param $modelQuery
     * @param RepositoryInterface $repository
     * @return mixed
     */
    public function apply($modelQuery, RepositoryInterface $repository)
    {
        $modelQuery->distinct();
        return $modelQuery;
    }

}
