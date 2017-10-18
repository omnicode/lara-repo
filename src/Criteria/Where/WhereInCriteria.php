<?php
namespace LaraRepo\Criteria\Where;

use LaraRepo\Contracts\RepositoryInterface;
use LaraRepo\Criteria\Criteria;

class WhereInCriteria extends Criteria
{
    /**
     * @var string
     */
    private $attribute;

    /**
     * @var array
     */
    private $values;

    /**
     * @param $attribute
     * @param array $values
     */
    public function __construct($attribute, $values = [])
    {
        if (!is_array($values)) {
            $values = [
                $values
            ];
        }

        $this->attribute = $attribute;
        $this->values = $values;
    }

    /**
     * @param $modelQuery
     * @param RepositoryInterface $repository
     * @return mixed
     */
    public function apply($modelQuery, RepositoryInterface $repository)
    {
        return $modelQuery->whereIn($repository->fixColumns($this->attribute), $this->values);
    }

}
