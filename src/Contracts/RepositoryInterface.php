<?php
namespace LaraRepo\Contracts;

interface RepositoryInterface
{
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
    public function getIndexableColumns($full = false, $hidden = true, $group = 'list');

    /**
     * @param bool $full
     * @param bool $hidden
     * @param string $group
     * @return mixed
     */
    public function getShowableColumns($full = false, $hidden = true, $group = 'list');

    /**
     * @return mixed
     */
    public function getSearchableColumns();

    /**
     * @return mixed
     */
    public function getListableColumns();

    /**
     * @param null $column
     * @param string $group
     * @return mixed
     */
    public function getSortableColumns($column = null, $group = 'list');

    /**
     * @return mixed
     */
    public function getStatusColumn();

    /**
     * @param bool $column
     * @param string $order
     * @param string $group
     * @return mixed
     */
    public function setSortingOptions($column = false, $order = 'asc', $group = 'list');

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
     * @param $field
     * @param $value
     * @return mixed
     */
    public function createWith(array $data, $field, $value);

    /**
     * @param $column
     * @param $value
     * @return mixed
     */
    public function increment($column, $value);

    /**
     * @param array $data
     * @param $id
     * @param string $attribute
     * @return mixed
     */
    public function update(array $data, $id, $attribute = "id");

    /**
     * @param array $data
     * @param array $conditions
     * @return mixed
     */
    public function updateAll(array $data, array $conditions);

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
    public function deleteBy($attribute, $value);

    /**
     * @param array $columns
     * @return mixed
     */
    public function all($columns = ['*']);

    /**
     * @param array $columns
     * @return mixed
     */
    public function first($columns = []);

    /**
     * @param array $columns
     * @return mixed
     */
    public function last($columns = []);

    /**
     * @param $id
     * @param array $columns
     * @return mixed
     */
    public function find($id, $columns = ['*']);

    /**
     * @param $id
     * @param array $columns
     * @return mixed
     */
    public function findForShow($id, $columns = []);

    /**
     * @param $attribute
     * @param $value
     * @param array $columns
     * @return mixed
     */
    public function findBy($attribute, $value, $columns = ['*']);

    /**
     * @param $attribute
     * @param $value
     * @param array $columns
     * @return mixed
     */
    public function findAllBy($attribute, $value, $columns = ['*']);

    /**
     * @param $id
     * @param $field
     * @return mixed
     */
    public function findField($id, $field);

    /**
     * @param $id
     * @return mixed
     */
    public function findFillable($id);

    /**
     * @param $id
     * @param array $options
     * @return mixed
     */
    public function findFillableWith($id, $options = []);

    /**
     * @param $id
     * @param $field
     * @param $value
     * @param string $cmp
     * @return mixed
     */
    public function findFillableWhere($id, $field, $value, $cmp = '=');

    /**
     * @param bool $active
     * @param array $columns
     * @return mixed
     */
    public function findList($active = true, $columns = []);

    /**
     * @param $attribute
     * @param $value
     * @param bool $active
     * @return mixed
     */
    public function findListBy($attribute, $value, $active = true);

    /**
     * @param int $perPage
     * @param array $columns
     * @param string $group
     * @return mixed
     */
    public function paginate($perPage = 15, $columns = ['*'], $group = 'list');

    /**
     * @param $field
     * @param $value
     * @param $cmp
     * @return mixed
     */
    public function paginateWhere($field, $value, $cmp);

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
     * @param $column
     * @param $value
     * @return mixed
     */
    public function existsWhere($column, $value);

}