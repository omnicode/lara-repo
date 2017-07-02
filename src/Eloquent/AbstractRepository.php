<?php
namespace LaraRepo\Eloquent;

use Illuminate\Container\Container as App;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use LaraTools\Utility\LaraUtil;

use LaraRepo\Contracts\CriteriaInterface;
use LaraRepo\Contracts\RepositoryInterface;
use LaraRepo\Contracts\TransactionInterface;
use LaraRepo\Criteria\Criteria;
use LaraRepo\Criteria\General\Order\SortCriteria;
use LaraRepo\Criteria\General\Select\SelectFillableCriteria;
use LaraRepo\Criteria\General\Select\SelectCriteria;
use LaraRepo\Criteria\General\Where\ActiveCriteria;
use LaraRepo\Criteria\General\Where\WhereCriteria;
use LaraRepo\Criteria\General\Where\WhereInCriteria;
use LaraRepo\Criteria\General\With\RelationCriteria;
use LaraRepo\Exceptions\RepositoryException;

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
     * @var App
     */
    private $app;

    /**
     * @param App $app
     * @param Collection $collection
     * @throws \LaraRepo\Exceptions\RepositoryException
     */
    public function __construct(App $app, Collection $collection)
    {
        $this->app = $app;
        $this->criteria = $collection;
        $this->resetScope();
        $this->makeModel();
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
     * @param bool $status
     * @return $this
     */
    public function skipCriteria($status = true)
    {
        $this->skipCriteria = $status;
        return $this;
    }

    /**
     * @return \Illuminate\Database\Eloquent\Builder
     * @throws RepositoryException
     */
    public function makeModel()
    {
        $model = $this->app->make($this->modelClass());

        if (!$model instanceof Model) {
            throw new RepositoryException("Class {$this->modelClass()} must be an instance of Illuminate\\Database\\Eloquent\\Model");
        }

        $this->model = $model;
        return $this->modelQuery = $model->newQuery();
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

        return LaraUtil::getFullColumns($columns, $table, $prefix);
    }

    /**
     * Specify Model class name
     *
     * @return mixed
     */
    public abstract function modelClass();

    /**
     * @param null $column
     * @param string $order
     * @return bool
     */
    public function setSortingOptions($column = null, $order = 'asc')
    {
        if ($column === null) {
            return true;
        }

        $column = strtolower($column);

        // check if column is allowed to be sorted
        if ($this->getSortableColumns($column)) {
            $order = strtolower($order);
            $order = $order == 'desc' ? $order : 'asc';
            $this->pushCriteria(new SortCriteria($column, $order));
        }
    }

    /**
     * returns the list of sortable fields
     *
     * @return mixed
     */
    public function getSortableColumns($column = null)
    {
        return $this->model->getSortable($column);
    }

    /**
     * @param Criteria $criteria
     * @return $this
     */
    public function pushCriteria(Criteria $criteria)
    {
        $this->criteria->push($criteria);
        return $this;
    }

    /**
     * @return mixed
     */
    public function getModel()
    {
        return $this->model;
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
     * returns the list of relations the model has
     *
     * @return array
     */
    public function getRelations()
    {
        return $this->model->_getRelations();
    }

    /**
     * @return string
     */
    public function getStatusColumn()
    {
        return $this->model->getStatusColumn();
    }

    /**
     * @param string $field
     * @param string $value
     * @param string $cmp
     * @return mixed
     */
    public function paginateWhere($field = '', $value = '', $cmp = '=')
    {
        $this->pushCriteria(new WhereCriteria($field, $value, $cmp));
        return $this->paginate();
    }

    /**
     * @param int $perPage
     * @param array $columns
     * @return mixed
     */
    public function paginate($perPage = 20, $columns = [])
    {
        if (empty($columns)) {
            $columns = $this->getIndexableColumns();
        }

        $this->applyCriteria();
        return $this->modelQuery->paginate($perPage, $this->fixColumns($columns));
    }

    /**
     * list of columns for showing on index page
     *
     * @return string
     */
    public function getIndexableColumns($full = null, $hidden = null)
    {
        return $this->model->getIndexable($full, $hidden);
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

    /**
     * @return mixed
     */
    public function getCriteria()
    {
        return $this->criteria;
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
     * @param array $data
     * @return mixed
     */
    public function create(array $data)
    {
        return $this->model->create($data);
    }

    /**
     * @param array $data
     * @param $field
     * @param $value
     * @return mixed
     */
    public function createWith(array $data = [], $field, $value)
    {
        $data[$field] = $value;
        return $this->create($data);
    }

    /**
     * @param array $data
     * @param $id
     * @param string $attribute
     * @return mixed
     */
    public function update(array $data, $id, $attribute = "id")
    {
        return $this->modelQuery->where($this->fixColumns($attribute), '=',
            $id)->update($data);
    }


    /**
     * @param array $data
     * @param array $conditions
     * @return mixed
     */
    public function updateAll(array $data, array $conditions)
    {
        $query = $this->modelQuery;

        foreach ($conditions as $attribute => $value) {
            if (is_array($value)) {
                $query->whereIn($attribute, $value);
            } else {
                $query->where($attribute, '=', $value);
            }
        }

        return $query->update($data);
    }

    /**
     * @param $id
     * @return mixed
     */
    public function delete($id)
    {
        return $this->find($id)->delete();
    }

    /**
     * @param array $columns
     * @return mixed
     */
    public function first($columns = [])
    {
        if (!empty($columns)) {
            $this->pushCriteria(new SelectCriteria($columns));
        }

        $this->applyCriteria();
        return $this->modelQuery->first();
    }

    /**
     * @param $id
     * @param $field
     * @return bool
     */
    public function findField($id, $field)
    {
        $data = $this->find($id, [$field]);

        if (!empty($data)) {
            return $data[$field];
        }

        return false;
    }

    /**
     * @param bool|false $active
     * @param array $listable
     * @return mixed
     */
    public function findList($active = true, $listable = [])
    {
        if (empty($listable)) {
            $listable = $this->getListableColumns();
        }

        if ($active) {
            $this->pushCriteria(new ActiveCriteria());
        }

        if (!empty($listable['relations'])) {
            $this->pushCriteria(new RelationCriteria($listable['relations']));
        }

        return $this->all($this->fixColumns($listable['columns']))->pluck($listable['value'], $listable['key'])->toArray();
    }

    /**
     * @param $attribute
     * @param $value
     * @param bool|true $active
     * @return mixed
     */
    public function findListBy($attribute, $value, $active = true)
    {
        $this->pushCriteria(new WhereCriteria($attribute, $value));
        return $this->findList($active);
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
     * @param array $columns
     * @return mixed
     */
    public function all($columns = ['*'])
    {
        if (!empty($columns) && getFirstValue($columns) != '*') {
            $this->pushCriteria(new SelectCriteria($columns));
        }

        $this->applyCriteria();
        return $this->modelQuery->get();
    }

    /**
     * @param $attribute
     * @param $value
     * @param array $columns
     * @return mixed
     */
    public function findBy($attribute, $value, $columns = ['*'])
    {
        $this->findByCriteria($attribute, $value, $columns);
        $this->applyCriteria();
        return $this->modelQuery->first();
    }

    /**
     * @param $attribute
     * @param $value
     * @param array $columns
     * @return mixed
     */
    public function findAllBy($attribute, $value, $columns = ['*'])
    {
        $this->findByCriteria($attribute, $value, $columns);
        $this->applyCriteria();
        return $this->modelQuery->get();
    }

    /**
     * @param $attribute
     * @param $value
     * @param array $columns
     */
    private function findByCriteria($attribute, $value, $columns = ['*'])
    {
        $this->pushCriteria(new WhereCriteria($attribute, $value));

        if (!empty($columns) && getFirstValue($columns) !== '*') {
            $this->pushCriteria(new SelectCriteria($columns));
        }
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
     * @param $id
     * @param array $columns
     * @return mixed
     */
    public function find($id, $columns = ['*'])
    {
        if (!empty($columns) && getFirstValue($columns) != '*') {
            $this->pushCriteria(new SelectCriteria($columns));
        }

        $this->applyCriteria();
        return $this->modelQuery->find($id);
    }

    /**
     * @param $id
     * @return bool
     */
    public function exists($id)
    {
        $this->pushCriteria(new WhereCriteria('id', $id));
        return $this->findCount() > 0 ? true : false;
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
     * @param $field
     * @param $value
     * @param string $cmp
     * @return mixed
     */
    public function findFillableWhere($id, $field, $value, $cmp = '=')
    {
        $this->pushCriteria(new WhereCriteria($field, $value, $cmp));
        return $this->findFillable($id);
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
