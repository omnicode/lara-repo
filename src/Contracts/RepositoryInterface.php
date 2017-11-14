<?php
namespace LaraRepo\Contracts;

interface RepositoryInterface
{

    public function makeModel();

    public function getModel();

    public function getTable();

    public function fixColumns($columns, $table = null, $prefix = null);

    public function getFillableColumns();

    public function getIndexableColumns($full = null, $hidden = null, $group = 'list');

    public function getSearchableColumns();

    public function getListableColumns();

    public function getSortableColumns($column = null, $group = 'list');

    public function getStatusColumn();

    public function setSortingOptions($column = null, $order = 'asc', $group = 'list');

    public function getRelations();


    public function saveAssociated($data, $options = [], $model = null);

    public function create(array $data);

    public function createWith(array $data, $field, $value);

    public function update(array $data, $id, $attribute = "id");

    public function updateAll(array $data, array $conditions);

    public function delete($id);

    public function all($columns = ['*']);

    public function first($columns);

    public function find($id, $columns = ['*']);

    public function findBy($attribute, $value, $columns = ['*']);

    public function findAllBy($attribute, $value, $columns = ['*']);

    public function findField($id, $field);

    public function findFillable($id);

    public function findFillableWith($id, $options = []);

    public function findFillableWhere($id, $field, $value, $cmp = '=');

    public function findList($active = true, $columns = []);

    public function findListBy($attribute, $value, $active = true);

    public function paginate($perPage = 15, $columns = ['*'], $group = 'list');

    public function paginateWhere($field, $value, $cmp);


    public function findCount($attribute, $value, $cmp);

    public function exists($id);

}