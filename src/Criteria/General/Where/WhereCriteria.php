<?php
namespace LaraRepo\Criteria\General\Where;

use LaraRepo\Contracts\RepositoryInterface;
use LaraRepo\Criteria\Criteria;

class WhereCriteria extends Criteria
{
    /**
     * @var string
     */
    private $attribute;

    /**
     * @var string
     */
    private $value;

    /**
     * @var string
     */
    private $comparison;

    /**
     * @param $attribute
     * @param $value
     * @param string $cmp
     */
    public function __construct($attribute, $value, $cmp = '=')
    {
        $this->attribute = $attribute;
        $this->value = $value;
        $this->comparison = $cmp;
    }

    /**
     * @param $modelQuery
     * @param RepositoryInterface $repository
     * @return mixed
     */
    public function apply($modelQuery, RepositoryInterface $repository)
    {
        return $modelQuery->where($repository->fixColumns($this->attribute), $this->comparison, $this->value);
    }

}
