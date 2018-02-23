<?php

namespace LaraRepo\Contracts;

interface RepositoryInterface
{
    const GROUP = 'list';

    /**
     * @return mixed
     */
    public function makeModel();

    /**
     * @return mixed
     */
    public function getModel();

    /**
     * @return mixed
     */
    public function getModelQuery();

    /**
     * @return mixed
     */
    public function resetModelQuery();

    /**
     * @return mixed
     */
    public function getTable();

    /**
     * @return mixed
     */
    public function getKeyName();

    /**
     * @param $columns
     * @param null $table
     * @param null $prefix
     * @return mixed
     */
    public function fixColumns($columns, $table = null, $prefix = null);

    /**
     * @return mixed
     */
    public function getFillableColumns();

    /**
     * @param bool $full
     * @param bool $hidden
     * @param string $group
     * @return mixed
     */
    public function getIndexableColumns($full = false, $hidden = true, $group = self::GROUP);

    /**
     * @param bool $full
     * @param bool $hidden
     * @param string $group
     * @return mixed
     */
    public function getShowableColumns($full = false, $hidden = true, $group = self::GROUP);

    /**
     * @return mixed
     */
    public function getListableColumns();

    /**
     * @param null $column
     * @param string $group
     * @return mixed
     */
    public function getSortableColumns($column = null, $group = self::GROUP);

    /**
     * @return mixed
     */
    public function getStatusColumn();

    /**
     * @param null $column
     * @param string $order
     * @param string $group
     * @return mixed
     */
    public function setSortingOptions($column = null, $order = 'asc', $group = self::GROUP);

    /**
     * @return mixed
     */
    public function getRelations();

    /**
     * @param $data
     * @param array $options
     * @param null $model
     * @return mixed
     */
    public function saveAssociated($data, $options = [], $model = null);

    /**
     * @param array $data
     * @return mixed
     */
    public function create(array $data);

    /**
     * @param array $data
     * @param $attribute
     * @param $value
     * @return mixed
     */
    public function createWith(array $data, $attribute, $value);

    /**
     * @param $column
     * @param int $value
     * @return mixed
     */
    public function increment($column, $value = 1);

    /**
     * @param $column
     * @param int $value
     * @return mixed
     */
    public function decrement($column, $value = 1);

    /**
     * @param array $data
     * @param $id
     * @param string $attribute
     * @return mixed
     */
    public function update(array $data, $id, $attribute = '_id');

    /**
     * @param array $data
     * @param array $conditions
     * @return mixed
     */
    public function updateBased(array $data, array $conditions);

    /**
     * @param $id
     * @return mixed
     */
    public function destroy($id);

    /**
     * @param $id
     * @return mixed
     */
    public function delete($id);

    /**
     * @param $attribute
     * @param $value
     * @return mixed
     */
    public function destroyBy($attribute, $value);

    /**
     * @param null $columns
     * @return mixed
     */
    public function all($columns = null);

    /**
     * @param null $columns
     * @return mixed
     */
    public function first($columns = null);

    /**
     * @param null $columns
     * @return mixed
     */
    public function last($columns = null);

    /**
     * @param $id
     * @param null $columns
     * @return mixed
     */
    public function find($id, $columns = null);

    /**
     * @param $id
     * @param null $columns
     * @return mixed
     */
    public function findForShow($id, $columns = null);

    /**
     * @param $attribute
     * @param $value
     * @param null $columns
     * @return mixed
     */
    public function findBy($attribute, $value, $columns = null);

    /**
     * @param $attribute
     * @param $value
     * @param null $columns
     * @return mixed
     */
    public function findAllBy($attribute, $value, $columns = null);

    /**
     * @param $id
     * @param $attribute
     * @return mixed
     */
    public function findAttribute($id, $attribute);

    /**
     * @param $id
     * @return mixed
     */
    public function findFillable($id);

    /**
     * @param $attribute
     * @param $value
     * @return mixed
     */
    public function findAllFillable($attribute, $value);

    /**
     * @param $id
     * @param array $related
     * @return mixed
     */
    public function findFillableWith($id, $related = []);

    /**
     * @param $id
     * @param $attribute
     * @param $value
     * @param string $cmp
     * @return mixed
     */
    public function findFillableWhere($id, $attribute, $value, $cmp = '=');

    /**
     * @param bool $active
     * @param array $listable
     * @return mixed
     */
    public function findList($active = true, $listable = null);

    /**
     * @param $attribute
     * @param $value
     * @param bool $active
     * @param null $listable
     * @return mixed
     */
    public function findListBy($attribute, $value, $active = true, $listable = null);

    /**
     * @param int $perPage
     * @param null $columns
     * @param string $group
     * @return mixed
     */
    public function paginate($perPage = 15, $columns = null, $group = self::GROUP);

    /**
     * @param $attribute
     * @param $value
     * @param $cmp
     * @return mixed
     */
    public function paginateWhere($attribute = '', $value = '', $cmp = '=');

    /**
     * @param null $attribute
     * @param null $value
     * @param string $cmp
     * @return mixed
     */
    public function findCount($attribute = null, $value = null, $cmp = '=');

    /**
     * @param $id
     * @return mixed
     */
    public function exists($id);

    /**
     * @param $attribute
     * @param $value
     * @return mixed
     */
    public function existsWhere($attribute, $value);
}
