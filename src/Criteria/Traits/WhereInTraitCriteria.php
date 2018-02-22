<?php

namespace LaraRepo\Criteria\Traits;

trait WhereInTraitCriteria
{
    /**
     * @param $query
     * @param $repository
     * @param $table
     * @return mixed
     */
    public function getWhereInQuery($query, $repository, $table, $where)
    {
        foreach ($where as $column => $values) {
            if (!is_array($values)) {
                $values = [
                    $values
                ];
            }
            $query->select($repository->fixColumns($column, $table))
                ->wherein($column, $values);
        }
        return $query;
    }
}
