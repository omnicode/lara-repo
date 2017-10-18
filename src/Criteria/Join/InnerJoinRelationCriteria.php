<?php
namespace LaraRepo\Criteria\Join;

use LaraRepo\Contracts\RepositoryInterface;
use LaraRepo\Criteria\Criteria;

class InnerJoinRelationCriteria extends Criteria
{
    /**
     * @var
     */
    protected $relation;

    /**
     * @var array
     */
    protected $otherKeys;

    /**
     * @param $relation
     * @param array $otherKeys
     */
    public function __construct($relation, $otherKeys = [])
    {
        if (!is_array($otherKeys)) {
            $otherKeys = [
                $otherKeys
            ];
        }

        $this->relation = $relation;
        $this->otherKeys = $otherKeys;
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
        if (strstr($this->relation, '.')) {
            $arr = explode('.', $this->relation);
            $firstRelation = $arr[0];
            $relation = $arr[1];

            if (!isset($relations[$firstRelation])) {
                throw new \Exception(sprintf('%s is not associated with %s', $repository->getTable(), $firstRelation));
            }
            $firstRelated = $relations[$firstRelation]->getRelated();

            $mainRelations = $firstRelated->_getRelations();
            if (!isset($mainRelations[$relation])) {
                throw new \Exception(sprintf('%s is not associated with %s', $firstRelated->getTable(), $relation));
            }

            $key = $repository->fixColumns($firstRelated->getKeyName(), $firstRelated->getTable());
            $related = $mainRelations[$relation];
        } else {
            $related = $relations[$this->relation];
            $key = $repository->fixColumns($repository->getModel()->getKeyName());
        }

        $table = $related->getTable();
        $otherKey = $related->getOtherKey();
        $foreignKey = $related->getForeignKey();

        $modelQuery->join($table, $key, '=', $foreignKey);
        if (!empty($this->otherKeys)) {
            $modelQuery->whereIn($otherKey, $this->otherKeys);
        }

        return $modelQuery;
    }

}