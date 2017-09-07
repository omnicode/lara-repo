<?php
namespace LaraRepo\Builder;


use Illuminate\Database\Query\Builder;

class LaraQueryBuilder extends Builder
{
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
        return parent::addSelect($column);
    }

}
