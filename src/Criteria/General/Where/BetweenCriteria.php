<?php
namespace LaraRepo\Criteria\General\Where;

use LaraRepo\Contracts\RepositoryInterface;
use LaraRepo\Criteria\Criteria;

class BetweenCriteria extends Criteria
{
    /**
     * @var string
     */
    private $column;

    /**
     * @var string
     */
    private $from;

    /**
     * @var string
     */
    private $to;

    /**
     * @param $column
     * @param $from
     * @param $to
     */
    public function __construct($column, $from, $to)
    {
        $this->column = $column;
        $this->from = $from;
        $this->to = $to;
    }

    /**
     * @param $modelQuery
     * @param RepositoryInterface $repository
     * @return mixed
     */
    public function apply($modelQuery, RepositoryInterface $repository)
    {
        return $modelQuery->whereBetween($repository->fixColumns($this->column), [$this->from, $this->to]);
    }

}
