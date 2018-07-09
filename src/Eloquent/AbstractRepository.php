<?php

namespace LaraRepo\Eloquent;

use Illuminate\Support\Facades\App;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use LaraRepo\Criteria\Limit\LimitCriteria;
use LaraRepo\Contracts\CriteriaInterface;
use LaraRepo\Contracts\RepositoryInterface;
use LaraRepo\Contracts\TransactionInterface;
use LaraRepo\Criteria\Criteria;
use LaraRepo\Criteria\Order\SortCriteria;
use LaraRepo\Criteria\Select\SelectFillableCriteria;
use LaraRepo\Criteria\Select\SelectCriteria;
use LaraRepo\Criteria\Where\ActiveCriteria;
use LaraRepo\Criteria\Where\WhereCriteria;
use LaraRepo\Criteria\Where\WhereInCriteria;
use LaraRepo\Criteria\With\RelationCriteria;
use LaraRepo\Exceptions\RepositoryException;
use LaraSupport\Facades\LaraDB;

abstract class AbstractRepository implements RepositoryInterface, CriteriaInterface, TransactionInterface
{
    /**
     * @var
     */
    protected $modelQuery;

    /**
     * @var
     */
    protected $model;

    /**
     * @var Collection
     */
    protected $criteria;

    /**
     * @var bool
     */
    protected $skipCriteria = false;

    /**
     * AbstractRepository constructor.
     * @param Collection $collection
     */
    public function __construct(Collection $collection)
    {
        $this->criteria = $collection;
        $this->resetScope();
        $this->makeModel();
    }

    /**
     * Specify Model class name
     *
     * @return mixed
     */
    public abstract function modelClass();


    /*************************************
     *        RepositoryInterface        *
     *************************************/

    /**
     * @return \Illuminate\Database\Eloquent\Builder
     * @throws RepositoryException
     */
    public function makeModel()
    {
        $model = App::make($this->modelClass());

        if (!$model instanceof Model) {
            throw new RepositoryException("Class {$this->modelClass()} must be an instance of Illuminate\\Database\\Eloquent\\Model");
        }

        $this->model = $model;
        $this->modelQuery = $model->newQuery();

        return $this->model;
    }

    /**
     * @return mixed
     */
    public function getModel()
    {
        return $this->model;
    }

    /**
     * @return mixed
     */
    public function getModelQuery()
    {
        return $this->modelQuery;
    }

    /**
     * @return $this
     */
    public function resetModelQuery()
    {
        $this->modelQuery = $this->model->newQuery();
        return $this;
    }

    /**
     * returns the table name for the given model
     * @return mixed
     */
    public function getTable()
    {
        return $this->model->getTable();
    }

    /**
     * @return mixed
     */
    public function getKeyName()
    {
        return $this->model->getKeyName();
    }

    /**
     * @param $columns
     * @param null $table
     * @param null $prefix
     * @return array
     */
    public function fixColumns($columns, $table = null, $prefix = null)
    {
        if (!$table) {
            $table = $this->getTable();
        }

        return LaraDB::getFullColumns($columns, $table, $prefix);
    }

    /**
     * returns the list of fillable fields
     *
     * @return array
     */
    public function getFillableColumns()
    {
        return $this->model->getFillable();
    }

    /**
     * list of columns for showing on index page
     *
     * @return string
     */
    public function getIndexableColumns($full = false, $hidden = true, $group = self::GROUP)
    {
        return $this->model->getIndexable($full, $hidden, $group);
    }

    /**
     * list of columns for showing on show page
     *
     * @return string
     */
    public function getShowableColumns($full = false, $hidden = true, $group = self::GROUP)
    {
        return $this->model->getShowable($full, $hidden, $group);
    }

    /**
     * columns used for model's find list
     *
     * @return mixed
     */
    public function getListableColumns()
    {
        return $this->model->getListable();
    }

    /**
     * returns the list of sortable fields
     *
     * @param null $column
     * @param string $group
     * @return mixed
     */
    public function getSortableColumns($column = null, $group = self::GROUP)
    {
        return $this->model->getSortable($column, $group);
    }

    /**
     * @return string
     */
    public function getStatusColumn()
    {
        return $this->model->getStatusColumn();
    }

    /**
     * @param null $column
     * @param string $order
     * @param string $group
     * @return bool
     */
    public function setSortingOptions($column = null, $order = 'asc', $group = self::GROUP)
    {
        if (is_null($column)) {
            return true;
        }

        $column = strtolower($column);

        // check if column is allowed to be sorted
        if ($this->getSortableColumns($column, $group)) {
            $order = strtolower($order);
            $order = ($order == 'desc') ? $order : 'asc';
            $this->pushCriteria(new SortCriteria($column, $order));
        }
    }

    /**
     * returns the list of relations the model has
     *
     * @return array
     */
    public function getRelations()
    {
        return $this->model->_getRelations();
    }


    /**
     * @param $data
     * @param array $options
     * @return mixed
     */
    public function saveAssociated($data, $options = [], $model = null)
    {
        return $this->model->saveAssociated($data, $options, $model);
    }

    /**
     * @param array $data
     * @return mixed
     */
    public function create(array $data)
    {
        return $this->model->create($data);
    }

    /**
     * @param array $data
     * @param $attribute
     * @param $value
     * @return mixed
     */
    public function createWith(array $data, $attribute, $value)
    {
        $data[$attribute] = $value;
        return $this->create($data);
    }

    /**
     * @param $column
     * @param int $value
     * @return mixed
     */
    public function increment($column, $value = 1)
    {
        $this->applyCriteria();
        return $this->modelQuery->increment($column, $value);
    }

    /**
     * @param $column
     * @param int $value
     * @return mixed
     */
    public function decrement($column, $value = 1)
    {
        $this->applyCriteria();
        return $this->modelQuery->decrement($column, $value);
    }

    /**
     * @param array $data
     * @param $id
     * @param string $attribute
     * @return mixed
     */
    public function update(array $data, $id, $attribute = '_id')
    {
        if ($attribute == '_id') {
            $attribute = $this->getKeyName();
        }

        $this->pushCriteria(new WhereCriteria($attribute, $id));
        $this->applyCriteria();
        return $this->modelQuery->update($data);
    }

    /**
     * @param array $data
     * @param array $conditions
     * @return mixed
     */
    public function updateBased(array $data, array $conditions)
    {
        foreach ($conditions as $attribute => $value) {
            if (is_array($value)) {
                $this->pushCriteria(new WhereInCriteria($attribute, $value));
            } else {
                $this->pushCriteria(new WhereCriteria($attribute, $value));
            }
        }

        $this->applyCriteria();
        return $this->modelQuery->update($data);
    }

    /**
     * @param $id
     * @return mixed
     */
    public function destroy($id)
    {
        $this->pushCriteria(new WhereCriteria($this->getKeyName(), $id));
        $this->applyCriteria();
        return $this->modelQuery->delete();
    }
    /**
     * @param $id
     * @return mixed
     */
    public function delete($id)
    {
        return $this->destroy($id);
    }

    /**
     * @param $attribute
     * @param $value
     * @return bool
     */
    public function destroyBy($attribute, $value)
    {
        $this->pushCriteria(new WhereCriteria($attribute, $value));
        $this->pushCriteria(new LimitCriteria(1));
        $this->applyCriteria();
        return $this->modelQuery->delete();
    }

    /**
     * @param $attribute
     * @param $value
     * @return bool
     */
    public function destroyAllBy($attribute, $value)
    {
        $this->pushCriteria(new WhereCriteria($attribute, $value));
        $this->applyCriteria();
        return $this->modelQuery->delete();
    }

    /**
     * @param null $columns
     * @return mixed
     */
    public function all($columns = null)
    {
        $this->fixSelectedColumns($columns);
        $this->applyCriteria();
        return $this->modelQuery->get();
    }

    /**
     * @param null $columns
     * @return mixed
     */
    public function first($columns = null)
    {
        $this->fixSelectedColumns($columns);
        $this->applyCriteria();
        return $this->modelQuery->first();
    }

    /**
     * @param null $columns
     * @return mixed
     */
    public function last($columns = null)
    {
        $this->fixSelectedColumns($columns);
        $id = $this->getKeyName();
        $this->pushCriteria(new SortCriteria($id, 'desc'));
        return $this->first($columns);
    }

    /**
     * @param $id
     * @param null $columns
     * @return mixed
     */
    public function find($id, $columns = null)
    {
        $this->fixSelectedColumns($columns);
        $this->applyCriteria();
        return $this->modelQuery->find($id);
    }

    /**
     * @param $id
     * @param array $columns
     * @return mixed
     */
    public function findForShow($id, $columns = null)
    {
        if (is_null($columns)) {
            $columns = $this->model->getShowAble();
        } elseif (is_array($columns)) {
            make_array($columns);
        }

        return $this->find($id, $columns);
    }

    /**
     * @param $attribute
     * @param $value
     * @param null $columns
     * @return mixed
     */
    public function findBy($attribute, $value, $columns = null)
    {
        $this->findByCriteria($attribute, $value, $columns);
        $this->applyCriteria();
        return $this->modelQuery->first();
    }

    /**
     * @param $attribute
     * @param $value
     * @param null $columns
     * @return mixed
     */
    public function findAllBy($attribute, $value, $columns = null)
    {
        $this->findByCriteria($attribute, $value, $columns);
        $this->applyCriteria();
        return $this->modelQuery->get();
    }

    /**
     * @param $id
     * @param $attribute
     * @return bool
     */
    public function findAttribute($id, $attribute)
    {
        $data = $this->find($id, [$attribute]);

        if (!empty($data[$attribute])) {
            return $data[$attribute];
        }

        return false;
    }

    /**
     * find by id - only fillable columns
     *
     * @param $id
     * @return mixed
     */
    public function findFillable($id)
    {
        $this->pushCriteria(new SelectFillableCriteria(true));
        return $this->find($id);
    }

    /**
     * @param $attribute
     * @param $value
     * @return mixed
     */
    public function findAllFillable($attribute, $value)
    {
        $this->pushCriteria(new SelectFillableCriteria(true));
        return $this->findAllBy($attribute, $value);
    }

    /**
     * @param $id
     * @param array $related
     * @return mixed
     */
    public function findFillableWith($id, $related = [])
    {
        if (!empty($related)) {
            $this->pushCriteria(new RelationCriteria($related));
        }

        return $this->findFillable($id);
    }

    /**
     * @param $id
     * @param $attribute
     * @param $value
     * @param string $cmp
     * @return mixed
     */
    public function findFillableWhere($id, $attribute, $value, $cmp = '=')
    {
        $this->pushCriteria(new WhereCriteria($attribute, $value, $cmp));
        return $this->findFillable($id);
    }

    /**
     * @param bool|false $active
     * @param array $listable
     * @return mixed
     */
    public function findList($active = true, $listable = null)
    {
        if (is_null($listable)) {
            $listable = $this->getListableColumns();
        }

        if ($active) {
            $this->pushCriteria(new ActiveCriteria());
        }

        if (!empty($listable['relations'])) {
            $this->pushCriteria(new RelationCriteria($listable['relations']));
        }

        return $this->all($this->fixColumns($listable['columns']))->pluck($listable['value'], $listable['key'])->all();
    }

    /**
     * @param $attribute
     * @param $value
     * @param bool $active
     * @param null $listable
     * @return mixed
     */
    public function findListBy($attribute, $value, $active = true, $listable = null)
    {
        $this->pushCriteria(new WhereCriteria($attribute, $value));
        return $this->findList($active, $listable);
    }

    /**
     * @param int $perPage
     * @param array $columns
     * @param string $group
     * @return mixed
     */
    public function paginate($perPage = 15, $columns = null, $group = self::GROUP)
    {
        if (is_null($columns)) {
            $columns = $this->getIndexableColumns(false, true, $group);
        }

        $this->applyCriteria();
        return $this->modelQuery->paginate($perPage, $this->fixColumns($columns));
    }

    /**
     * @param string $attribute
     * @param string $value
     * @param string $cmp
     * @return mixed
     */
    public function paginateWhere($attribute = '', $value = '', $cmp = '=')
    {
        $this->pushCriteria(new WhereCriteria($attribute, $value, $cmp));
        return $this->paginate();
    }

    /**
     * @param null $attribute
     * @param null $value
     * @param string $cmp
     * @return mixed
     */
    public function findCount($attribute = null, $value = null, $cmp = '=')
    {
        if (!empty($attribute) && !empty($value)) {
            if (is_array($value)) {
                $this->pushCriteria(new WhereInCriteria($attribute, $value));
            } else {
                $this->pushCriteria(new WhereCriteria($attribute, $value, $cmp));
            }
            $this->pushCriteria(new SelectCriteria($attribute));
        }

        $this->applyCriteria();
        return $this->modelQuery->count();
    }

    /**
     * @param $id
     * @return bool
     */
    public function exists($id)
    {
        $primaryKey = $this->getKeyName();
        return $this->existsWhere($primaryKey, $id);
    }

    /**
     * @param $attribute
     * @param $value
     * @return bool
     */
    public function existsWhere($attribute, $value)
    {
        $this->pushCriteria(new WhereCriteria($attribute, $value));
        return $this->findCount() > 0;
    }

    /**
     * @param $attribute
     * @param $value
     * @param array $columns
     */
    protected function findByCriteria($attribute, $value, $columns = null)
    {
        $this->pushCriteria(new WhereCriteria($attribute, $value));
        $this->fixSelectedColumns($columns);
    }

    /**
     * @param null $columns
     */
    protected function fixSelectedColumns($columns = null)
    {
        if (is_null($columns)) {
            $columns = array_merge([$this->getKeyName()], $this->getFillableColumns());
        }
        $columns = (array) $columns;

        $this->pushCriteria(new SelectCriteria($columns));
    }

    /*************************************
     *         CriteriaInterface         *
     *************************************/

    /**
     * @return mixed
     */
    public function getCriteria()
    {
        return $this->criteria;
    }

    /**
     * @param Criteria $criteria
     * @return $this
     */
    public function getByCriteria(Criteria $criteria)
    {
        $this->modelQuery = $criteria->apply($this->modelQuery, $this);
        return $this;
    }

    /**
     * @param bool $resetQuery
     * @return $this
     */
    public function resetCriteria($resetQuery = true)
    {
        $this->criteria = App::make(Collection::class);

        if ($resetQuery) {
            $this->resetModelQuery();
        }

        return $this;
    }

    /**
     * @return $this
     */
    public function resetScope()
    {
        $this->skipCriteria(false);
        return $this;
    }

    /**
     * @param Criteria $criteria
     * @return $this
     */
    public function pushCriteria(Criteria $criteria)
    {
        if (!$this->criteria->contains($criteria)) {
            $this->criteria->push($criteria);
        }
        return $this;
    }

    /**
     * @param bool $status
     * @return $this
     */
    public function skipCriteria($status = true)
    {
        $this->skipCriteria = $status;
        return $this;
    }

    /**
     * @return $this
     */
    public function applyCriteria()
    {
        if ($this->skipCriteria === true) {
            return $this;
        }

        foreach ($this->getCriteria() as $criteria) {
            if ($criteria instanceof Criteria) {
                $this->modelQuery = $criteria->apply($this->modelQuery, $this);
            }
        }

        return $this;
    }

    /**************************************
     *        TransactionInterface        *
     **************************************/

    /**
     * @return mixed
     */
    public function startTransaction()
    {
        return DB::beginTransaction();
    }

    /**
     * @return mixed
     */
    public function commitTransaction()
    {
        return DB::commit();
    }

    /**
     * @return mixed
     */
    public function rollbackTransaction() {
        return DB::rollBack();
    }
}
