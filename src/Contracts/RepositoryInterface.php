<?php
namespace LaraRepo\Contracts;

interface RepositoryInterface
{

    public function getModel();

    public function getTable();

    public function makeModel();

    public function fixColumns($columns, $table = null, $prefix = null);

    public function getFillableColumns();

    public function getIndexableColumns($full = null, $hidden = null);

    public function getListableColumns();

    public function getSortableColumns($column = null);

    public function setSortingOptions();

    public function getRelations();

    public function all($columns = ['*']);

    public function paginate($perPage = 15, $columns = ['*']);

    public function paginateWhere($field, $value, $cmp);

    public function create(array $data);

    public function createWith(array $data, $field, $value);

    public function update(array $data, $id, $attribute);

    public function updateAll(array $data, array $conditions);

    public function delete($id);

    public function first($columns);

    public function findField($id, $field);

    public function find($id, $columns = ['*']);

    public function exists($id);

    public function findCount($attribute, $value, $cmp);

    public function findFillable($id);

    public function findFillableWith($id, $options = []);

    public function findFillableWhere($id, $field, $value, $cmp = '=');

    public function findList($active = true, $columns = []);

    public function findListBy($attribute, $value, $active = true);

    public function findBy($attribute, $value, $columns = ['*']);

    public function findAllBy($attribute, $value, $columns = ['*']);

    public function saveAssociated($data, $options = [], $model = null);

    public function getStatusColumn();

}