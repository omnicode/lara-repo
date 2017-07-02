<?php
namespace LaraRepo\Criteria\General\Select;

use LaraRepo\Contracts\RepositoryInterface;
use LaraRepo\Criteria\Criteria;

class FillableCriteria extends Criteria
{
    /**
     * @var mixed
     */
    private $include = null;

    /**
     * @var bool
     */
    private $exclude = null;

    /**
     * @param array $include
     * @param array $exclude
     */
    public function __construct($include = [], $exclude = [])
    {
        if (is_string($include)) {
            $include = [
                $include
            ];
        }

        if (is_string($exclude)) {
            $exclude = [
                $exclude
            ];
        }

        $this->include = $include;
        $this->exclude = $exclude;
    }

    /**
     * @param $modelQuery
     * @param RepositoryInterface $repository
     * @return mixed
     */
    public function apply($modelQuery, RepositoryInterface $repository)
    {
        $columns = $repository->getFillableColumns();

        if ($this->include === true) {
            $this->include = [
                $repository->getModel()->getKeyName()
            ];
        }

        $columns = array_merge($columns, $this->include);

        if (!empty($this->exclude)) {
            $columns = array_diff($columns, $this->exclude);
        }

        return $modelQuery->addSelect($repository->fixColumns($columns));
    }

}
