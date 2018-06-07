<?php
namespace LaraRepo\Criteria\With;

use LaraRepo\Contracts\RepositoryInterface;
use LaraRepo\Criteria\Criteria;
use LaraSupport\LaraDB;

// @TODO - add relation options
class RelationCriteria extends Criteria
{
    /**
     * @var array
     */
    private $relations;

    /**
     * @param array $relations
     */
    public function __construct($relations = [])
    {
        if (!is_array($relations)) {
            $relations = [
                $relations
            ];
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

        // relations for intermediate tables
        $extraRelations = [];

        // either, 'table_name' => [] format || '0' => 'table_name'
        foreach ($this->relations as $k => $v) {
            if (is_numeric($k)) {
                $k = $v;
                $v = [];
            }

            $foreignKeyColumn = false;
            // select only primary key and foreign key to prevent select * for intermediate table
            if (strstr($k, '.')) {
                $relationList = explode('.', $k);
                $firstRelation = $relationList[0];
                $secondRelation = $relationList[1];

                if (!isset($modelRelations[$firstRelation])) {
                    throw new \Exception(sprintf('%s is not associated with %s', $repository->getTable(),
                        $firstRelation));
                    break;
                }

                if (!isset($v['intermediate']) || $v['intermediate'] !== false) {
                    $extraRelations[$firstRelation] = function ($query) {
                        $firstRelated = $query->getRelated();
                        $columns = [
                            $firstRelated->getKeyName(),
                            $query->getForeignKey()
                        ];

                        $columns = LaraDB::getFullColumns($columns, $firstRelated->getTable());
                        $query->addSelect($columns);
                    };
                }

                $relation = $modelRelations[$firstRelation];
            } else {
                $relation = $modelRelations[$k];

                // @TODO - create constants by relationship names
                if (in_array(class_basename($relation), ['HasMany', 'BelongsToMany'])) {
                    $foreignKeyColumn = $this->getRelationForeginKeyName($relation);
                }
            }

            /**
             * @TODO - check if relations exists !!!
             */
            $columns = [];
            if (!empty($v['columns'])) {
                $columns = $v['columns'];
            }

            if (!is_array($columns)) {
                $columns = [
                    $columns
                ];
            }

            if ($foreignKeyColumn) {
                // always add foreign key column - otherwise relations are returned as empty
                $columns[] = $foreignKeyColumn;
            }

            // check conditions
            $where = [];
            if (!empty($v['where'])) {
                $where = $v['where'];
            }

            $orders = false;
            if (!empty($v['order'])) {
                $orders = $v['order'];
            }

            $relations[$k] = function ($query) use ($columns, $where, $orders) {
                $columns = LaraDB::getFullColumns($columns, $query->getRelated()->getTable());
                $query->addSelect($columns);

                if ($orders) {
                    foreach ($orders as $field => $type) {
                        $query->orderBy(LaraDB::getFullColumns($field, $query->getRelated()->getTable()), $type);
                    }
                }

                if (!empty($where)) {
                    foreach ($where as $col => $vals) {
                        if (!is_array($vals)) {
                            $vals = [
                                $vals
                            ];
                        }
                        $query->whereIn(LaraDB::getFullColumns($col, $query->getRelated()->getTable()), $vals);
                    }
                }
            };
        }

        return $modelQuery->with(array_merge($extraRelations, $relations));
    }

    /**
     * @param $relation
     * @return mixed
     */
    private function getRelationForeginKeyName($relation) {
        if (in_array('getQualifiedRelatedPivotKeyName', get_class_methods($relation))) {
           return $relation->getQualifiedRelatedPivotKeyName();
        }
        
        if(in_array('getQualifiedForeignKeyName', get_class_methods($relation))) {
            // for Laravel 5.4
            return $relation->getQualifiedForeignKeyName();
        }
        
        // for Laravel < 5.4
        return $relation->getForeignKey();
    }
}
