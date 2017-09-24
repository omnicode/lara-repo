<?php
namespace LaraRepo\Builder;

use Illuminate\Database\Query\Builder;

class LaraQueryBuilder extends Builder
{
    /***
     * @param array|mixed $column
     * @return $this
     */
    public function addSelect($column)
    {
        if (!is_array($column)) {
            $column = [$column];
        }
        foreach ($column  as $index =>  $col) {
            if (is_array($this->columns) && in_array($col, $this->columns)) {
                unset($column[$index]);
            }
        }
        parent::addSelect($column);
        return $this;
    }

    /**
     * @param array|\Closure|string $column
     * @param null $operator
     * @param null $value
     * @param string $boolean
     * @return $this
     * @throws \Exception
     */
    public function where($column, $operator = null, $value = null, $boolean = 'and')
    {
        $parentCall = true;
        if (!empty($this->wheres)) {
            foreach ($this->wheres as $where) {
                if ($where['type'] != 'basic') {
                    continue;
                }

                if($column == $where['column'] && $operator == $where['operator'] && $boolean == $where['boolean']) {
                    if ($value == $where['value']) {
                        $parentCall = false;
                        break;
                    }

                    if ($boolean == 'and') {
                        throw new \Exception('In where query the ' . $column . ' cann\'t the same time have 2 values old = '
                            . $where['value'] . ', new ' . $value . '');
                    }
                }
            }
        }
        if ($parentCall) {
            parent::where(...func_get_args());
        }
        return $this;
    }


}
