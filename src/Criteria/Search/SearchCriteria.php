<?php

namespace LaraRepo\Criteria\Search;

use LaraRepo\Contracts\RepositoryInterface;
use LaraRepo\Criteria\Criteria;

class SearchCriteria extends Criteria
{
    /**
     * @var
     */

    private $table;
    /**
     * @var
     */
    private $columns;

    /**
     * @var
     */
    private $value;

    /**
     * SearchCriteria constructor.
     * @param $value
     * @param null $columns
     * @param null $table
     */
    public function __construct($value, $columns = null, $table = null)
    {
        $this->table = $table;
        $this->columns = $columns;
        $this->value = $value;
    }

    /**
     * @param $modelQuery
     * @param RepositoryInterface $repository
     * @return mixed
     */
    public function apply($modelQuery, RepositoryInterface $repository)
    {
        if(empty($this->table)) {
            $this->table = $repository->getTable();
        }

        if (empty($this->columns)) {
            $this->columns = $repository->getSearchableColumns();
        } elseif (!is_array($this->columns)) {
            $this->columns = [$this->columns];
        }

        $this->columns = $repository->fixColumns($this->columns);

        $modelQuery->where(function ($query) {
            foreach ($this->columns as $column) {
                $query->orWhere($column, 'LIKE', '%' . $this->value . '%');
            }
        });


//            if(is_array($column)) {
//                dd('TODO for relation');
//            } else {
//
//            }

        return $modelQuery;
    }
}
