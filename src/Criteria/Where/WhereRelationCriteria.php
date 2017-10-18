<?php
namespace LaraRepo\Criteria\Where;

use LaraRepo\Contracts\RepositoryInterface;
use LaraRepo\Criteria\Criteria;

class WhereRelationCriteria extends Criteria
{

    /**
     * @var
     */
    private $relation;

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
     * @param $relation
     * @param $attribute
     * @param $value
     * @param string $cmp
     */
    public function __construct($relation, $attribute, $value, $cmp = '=')
    {
        $this->relation = $relation;
        $this->attribute = $attribute;
        $this->value = $value;
        $this->comparison = $cmp;
    }

    /**
     * @param $modelQuery
     * @param RepositoryInterface $repository
     * @return mixed
     * @throws \Exception
     */
    public function apply($modelQuery, RepositoryInterface $repository)
    {
        $relations = $repository->getRelations();

        if (!isset($relations[$this->relation])) {
            throw new \Exception(sprintf('%s is not associated with %s', $repository->getTable(), $this->relation));
        }

        $related = $relations[$this->relation]->getRelated();
        $table = $related->getTable();

        return $modelQuery->where($repository->fixColumns($this->attribute, $table), $this->comparison, $this->value);
    }

}
