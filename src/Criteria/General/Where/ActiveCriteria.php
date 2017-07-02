<?php
namespace LaraRepo\Criteria\General\Where;

use LaraRepo\Contracts\RepositoryInterface;
use LaraRepo\Criteria\Criteria;
use LaraTools\Utility\LaraUtil;

class ActiveCriteria extends Criteria
{

    public function apply($modelQuery, RepositoryInterface $repository)
    {
        $column = $repository->getStatusColumn();
        if ($column && LaraUtil::hasColumn($repository->getTable(), $column)) {
            return $modelQuery->where(
                $repository->fixColumns($repository->getStatusColumn()), '=',
                \ConstGeneralStatus::Active);
        }

        return $modelQuery;
    }

}
