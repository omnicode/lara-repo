<?php

namespace LaraRepo\Criteria\Traits;

use Illuminate\Database\Eloquent\RelationNotFoundException;
use LaraRepo\Contracts\RepositoryInterface;

trait RelationTraitCriteria
{

    /**
     * @param RepositoryInterface $repository
     * @return mixed
     */
    public function getRelationTable(RepositoryInterface $repository)
    {
        $relations = $repository->getRelations();

        if (!isset($relations[$this->relation])) {
            throw new RelationNotFoundException(sprintf('%s is not related to %s table', $repository->getTable(),
                $this->relation));
        }

        $related = $repository->getRelations()[$this->relation];
        return $related->getRelated()->getTable();
    }
}
