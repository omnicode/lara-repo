<?php
namespace LaraRepo\Criteria\Has;

use LaraRepo\Contracts\RepositoryInterface;
use LaraRepo\Criteria\Criteria;

class HasCriteria extends Criteria
{
    /**
     * @var
     */
    private $columns;

    /**
     * @var
     */
    private $value;

    /**
     * @var string
     */
    private $cmp;

    /***
     * HasCriteria constructor.
     * @param $columns
     * @param $value
     * @param string $cmp
     */
    public function __construct($columns, $value, $cmp = '=')
    {
        $this->columns = $columns;
        $this->value = $value;
        $this->cmp = $cmp;
    }

    /***
     * @param $modelQuery
     * @param RepositoryInterface $repository
     * @return mixed
     */
    public function apply($modelQuery, RepositoryInterface $repository)
    {
        $modelQuery->has($this->columns, $this->cmp,$this->value);
        return $modelQuery;
    }

}
