<?php

namespace LaraRepo\Criteria\Limit;

use LaraRepo\Contracts\RepositoryInterface;
use LaraRepo\Criteria\Criteria;

class LimitCriteria extends Criteria
{
    /**
     * @var
     */
    private $limit;

    /***
     * LimitCriteria constructor.
     * @param $limit
     */
    public function __construct($limit)
    {
        $this->limit= $limit;
    }

    /***
     * @param $modelQuery
     * @param RepositoryInterface $repository
     * @return mixed
     */
    public function apply($modelQuery, RepositoryInterface $repository)
    {
        $modelQuery->limit($this->limit);
        return $modelQuery;
    }
}
