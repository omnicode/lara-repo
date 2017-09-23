<?php

namespace LaraRepo\Criteria\With;

use LaraRepo\Contracts\RepositoryInterface;
use LaraRepo\Criteria\Criteria;

class WithCountCriteria extends Criteria
{
    /**
     * @var
     */
    private $relations;

    /**
     * WithCountCriteria constructor.
     * @param $relations
     */
    public function __construct($relations)
    {
        if (!is_array($relations)) {
            $relations = [$relations];
        }
        $this->relations = $relations;
    }

    /**
     * @param $modelQuery
     * @param RepositoryInterface $repository
     * @return mixed
     * @throws \Exception
     */
    public function apply($modelQuery, RepositoryInterface $repository)
    {
        // check relations
        $modelRelations = $repository->getRelations();
        foreach ($this->relations as $relation => $data) {

            if (is_numeric($relation)) {
                $relation = $data;
            }

            if (empty($modelRelations[$relation])) {
                throw new \Exception(sprintf('%s is not associated with %s', $repository->getTable(), $relation ));
            }

            $modelQuery->withCount([$relation . ' AS ' . $repository->fixColumns($relation) => function($query) {

            }]);

            if (!empty($data['count_of'])) {
                foreach ($data['count_of'] as $preCountName => $countData) {
                    if (is_numeric($preCountName)) {
                        $preCountName = $countData;
                    }
                    $modelQuery->withCount(
                        [
                            $relation . ' AS ' . $repository->fixColumns($preCountName ) => function ($query) use ($countData) {
                                if (!empty($countData['where'])) {
                                    $where = $countData['where'];
                                    $query->where($where[0], $where[2], $where[1]);
                                }
                            }
                        ]
                    );

                }
            }

            if (!empty($data['order'])) {
                $modelQuery->orderBy($relation . '_count', $data['order']);
            }

        }

        return $modelQuery;
    }

}
