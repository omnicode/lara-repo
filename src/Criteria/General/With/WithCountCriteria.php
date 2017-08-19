<?php
namespace LaraRepo\Criteria\General\With;

use LaraRepo\Contracts\RepositoryInterface;
use LaraRepo\Criteria\Criteria;
use LaraRepo\Criteria\General\With\RelationCriteria;
use MyDevData\Scopes\AccountScope;

class WithCountCriteria extends Criteria
{
    // @ TODO move class in LaraRepo
    /**
     * @var
     */
    protected $relation;

    /**
     * @var
     */
    protected $column;

    /**
     * @var array
     */
    protected $values;

    /**
     * @var
     */
    protected $withoutGlobalScope;

    /**
     * @param $relation
     * @param string $column
     * @param array $values
     */
    public function __construct($relation, $column = '', $values = [], $withoutGlobalScope = [])
    {
        if (!is_array($values)) {
            $values = [
                $values
            ];
        }

        $this->values = $values;
        $this->column = $column;
        $this->relation = $relation;
        $this->withoutGlobalScope = $withoutGlobalScope;
    }

    /**
     * @param $modelQuery
     * @param RepositoryInterface $repository
     * @return mixed
     */
    public function apply($modelQuery, RepositoryInterface $repository)
    {
        if ($this->withoutGlobalScope) {

            if (!is_array($this->withoutGlobalScope)) {
                $this->withoutGlobalScope = [$this->withoutGlobalScope];
            }

            foreach ($this->withoutGlobalScope as $globalScope) {
                $modelQuery->withoutGlobalScope($globalScope);
            }

        }

        $modelQuery->withCount([$this->relation => function($query) {

            $query->whereIn($this->column, $this->values);

            if ($this->withoutGlobalScope ) {
                foreach ($this->withoutGlobalScope as $globalScope) {
                    $query->withoutGlobalScope($globalScope);
                }
            }

        }]);

        return $modelQuery;
    }

}
