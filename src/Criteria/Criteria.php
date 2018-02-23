<?php

namespace LaraRepo\Criteria;

use LaraRepo\Contracts\RepositoryInterface;

abstract class Criteria
{
    /**
     * @param $modelQuery
     * @param RepositoryInterface $repository
     * @return mixed
     */
    public abstract function apply($modelQuery, RepositoryInterface $repository);
}
