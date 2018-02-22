<?php

namespace LaraRepo\Criteria\Offset;

use LaraRepo\Contracts\RepositoryInterface;
use LaraRepo\Criteria\Criteria;

class OffsetCriteria extends Criteria
{
    /**
     * @var
     */
    private $offset;

    /***
     * OffsetCriteria constructor.
     * @param $offset
     */
    public function __construct($offset)
    {
        $this->offset= $offset;
    }

    /**
     * @param $modelQuery
     * @param RepositoryInterface $repository
     * @return mixed
     */
    public function apply($modelQuery, RepositoryInterface $repository)
    {
        $modelQuery->offset($this->offset);
        return $modelQuery;
    }
}