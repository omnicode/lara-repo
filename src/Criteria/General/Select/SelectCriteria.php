<?php
namespace LaraRepo\Criteria\General\Select;

use LaraRepo\Contracts\RepositoryInterface;
use LaraRepo\Criteria\Criteria;

class SelectCriteria extends Criteria
{
    /**
     * @var array
     */
    private $columns;

    /**
     * @var
     */
    private $table;

    /**
     * @param array $columns
     * @param $table
     */
    public function __construct($columns = [], $table = null)
    {
        if (!is_array($columns)) {
            $columns = [
                $columns
            ];
        }

        $this->columns = $columns;
        $this->table = $table;
    }

    /**
     * @param $modelQuery
     * @param RepositoryInterface $repository
     * @return mixed
     */
    public function apply($modelQuery, RepositoryInterface $repository)
    {
        if (empty($this->table)) {
            $this->table = $repository->getTable();
        }

        return $modelQuery->addSelect($repository->fixColumns($this->columns, $this->table));
    }

}
