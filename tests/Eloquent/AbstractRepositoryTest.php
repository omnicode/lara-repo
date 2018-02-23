<?php

namespace Tests\Eloquent;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\DB;
use LaraRepo\Criteria\Criteria;
use LaraRepo\Criteria\Limit\LimitCriteria;
use LaraRepo\Criteria\Order\SortCriteria;
use LaraRepo\Criteria\Select\SelectCriteria;
use LaraRepo\Criteria\Where\WhereCriteria;
use LaraRepo\Criteria\Where\WhereInCriteria;
use LaraRepo\Criteria\With\RelationCriteria;
use LaraRepo\Eloquent\AbstractRepository;
use LaraRepo\Exceptions\RepositoryException;
use LaraTest\Traits\AccessProtectedTraits;
use LaraTest\Traits\AssertionTraits;
use LaraTest\Traits\MockTraits;
use Tests\TestCase;

class AbstractRepositoryTest extends TestCase
{
    use MockTraits, AccessProtectedTraits, AssertionTraits;

    /**
     * @var
     */
    protected $abstractRepository;

    /**
     * @var
     */
    protected $model;

    /**
     * @var
     */
    protected $modelQuery;


    /**
     *
     */
    public function setUp()
    {
        parent::setUp();
        $this->abstractRepository = $this->getMockAbstractRepositoryWithMockedMethods();
        $this->setModelInAbstractRepository();
        $this->setModelQueryInAbstractRepository();
    }

    /**
     *
     */
    public function testConstructMethod()
    {
        $this->assertInstanceOf(Collection::class, $this->abstractRepository->getCriteria());
    }

    /**
     *
     */
    public function testModelClass()
    {
        $this->methodWillReturnTrue($this->abstractRepository, 'modelClass');
        $this->assertTrue($this->abstractRepository->modelClass());
    }

    /**
     *
     */
    public function testMakeModel()
    {
        $abstractRepository= $this->getMockForAbstractClass(
            AbstractRepository::class,
            [app(Collection::class)],
            '',
            false
            ['modelClass']
        );

        $model = $this->getMockForAbstract(Model::class, [], ['newQuery']);
        $this->methodWillReturn($model, 'newQuery', 'modelQuery');
        $this->methodWillReturn($abstractRepository, 'modelClass', Model::class);

        App::shouldReceive('make')
            ->once()
            ->with(Model::class)
            ->andReturn($model);

        $this->assertInstanceOf(Model::class, $abstractRepository->makeModel());
        $this->assertEquals('modelQuery', $abstractRepository->getModelQuery());
    }

    /**
     *
     */
    public function testMakeModelWithException()
    {
        $exceptionMessage = 'Class model must be an instance of Illuminate\Database\Eloquent\Model';
        $abstractRepository= $this->getMockForAbstractClass(
            AbstractRepository::class,
            [app(Collection::class)],
            '',
            false
            ['modelClass']
        );

        $this->methodWillReturn($abstractRepository, 'modelClass', 'model', [], 'any');
        $model = new \stdClass();
        App::shouldReceive('make')
            ->once()
            ->with('model')
            ->andReturn($model);

        $this->expectException(RepositoryException::class);
        $this->expectExceptionMessage($exceptionMessage);
        $abstractRepository->makeModel();
    }

    /**
     *
     */
    public function testGetModel()
    {
        $model = 'model';
        $this->setProtectedAttributeOf($this->abstractRepository, 'model', $model);
        $this->assertEquals($model, $this->abstractRepository->getModel());
    }

    /**
     *
     */
    public function testGetModelQuery()
    {
        $modelQuery = 'modelQuery';
        $this->setProtectedAttributeOf($this->abstractRepository, 'modelQuery', $modelQuery);
        $this->assertEquals($modelQuery, $this->abstractRepository->getModelQuery());
    }

    /**
     *
     */
    public function testResetModelQuery()
    {
        $model = $this->getMockObjectWithMockedMethods('newQuery');
        $this->methodWillReturn($model, 'newQuery', 'modelQuery');
        $this->setProtectedAttributeOf($this->abstractRepository, 'model', $model);
        $this->assertEquals($model, $this->abstractRepository->getModel());
        $this->assertInstanceOf(AbstractRepository::class, $this->abstractRepository->resetModelQuery());
        $this->assertEquals('modelQuery', $this->abstractRepository->getModelQuery());
    }

    /**
     *
     */
    public function testGetTable()
    {
        $this->methodWillReturnTrue($this->model, 'getTable');
        $this->assertTrue($this->abstractRepository->getTable());
    }

    /**
     *
     */
    public function testGetKeyName()
    {
        $this->methodWillReturnTrue($this->model, 'getKeyName');
        $this->assertTrue($this->abstractRepository->getKeyName());
    }

    /**
     *
     */
    public function testFixColumnsWhenTableIsEmpty()
    {
        $columns = [];
        $abstractRepository = $this->getMockAbstractRepositoryWithMockedMethods(['getTable']);
        $this->expectCallMethod($abstractRepository, 'getTable');
        $abstractRepository->fixColumns($columns);
    }

//     /**
//      *
//      */
//     public function testFixColumnsWhenTableIsEmptyEnd()
//     {
//         $columns = ['col'];
//         $abstractRepository = $this->getMockAbstractRepositoryWithMockedMethods(['getTable']);
//         $this->methodWillReturn($abstractRepository, 'getTable', 'table');
//         $this->assertEquals(['table.col'], $abstractRepository->fixColumns($columns));
//     }

//     /**
//      *
//      */
//     public function testFixColumnsWhenTableIsNotEmpty()
//     {
//         $table = 'table';
//         $columns = ['column'];
//         $expected = [$table. '.column'];
//         $abstractRepository = $this->getMockAbstractRepositoryWithMockedMethods(['getTable']);
//         $this->assertEquals($expected, $abstractRepository->fixColumns($columns, $table));
//     }

    /**
     *
     */
    public function testGetFillableColumns()
    {
        $this->methodWillReturnTrue($this->model, 'getFillable');
        $this->assertTrue($this->abstractRepository->getFillableColumns());
    }

    /**
     *
     */
    public function testGetIndexableColumns()
    {
        $full = false;
        $hidden = true;
        $group = 'list';
        $this->methodWillReturnTrue($this->model, 'getIndexable', [$full, $hidden, $group]);
        $this->assertTrue($this->abstractRepository->getIndexableColumns($full, $hidden, $group));
    }

    /**
     *
     */
    public function testGetShowableColumns()
    {
        $full = false;
        $hidden = true;
        $group = 'list';

        $this->methodWillReturnTrue($this->model, 'getShowable', [$full, $hidden, $group]);
        $this->assertTrue($this->abstractRepository->getShowableColumns($full, $hidden, $group));
    }

    /**
     *
     */
    public function testGetListableColumns()
    {
        $this->methodWillReturnTrue($this->model, 'getListable');
        $this->assertTrue($this->abstractRepository->getListableColumns());
    }

    /**
     *
     */
    public function testGetSortableColumns()
    {
        $column = 'column';
        $group = 'list';

        $this->methodWillReturnTrue($this->model, 'getSortable', [$column, $group]);
        $this->assertTrue($this->abstractRepository->getSortableColumns($column, $group));
    }

    /**
     *
     */
    public function testGetStatusColumn()
    {
        $this->methodWillReturnTrue($this->model, 'getStatusColumn');
        $this->assertTrue($this->abstractRepository->getStatusColumn());
    }

    /**
     *
     */
    public function testSetSortingOptions_WhetColumnIsFalse()
    {
        $this->assertTrue($this->abstractRepository->setSortingOptions());
    }

    /**
     *
     */
    public function testSetSortingOptions_WhetColumnIsNotFalseFalse_GetSortableColumnsIsFalse()
    {
        $abstractRepository = $this->getMockAbstractRepositoryWithMockedMethods('getSortableColumns');
        $this->assertNull($abstractRepository->setSortingOptions(true));
    }

    /**
     *
     */
    public function testSetSortingOptions_WhetColumnIsNotFalseFalse_GetSortableColumnsIsTrue()
    {
        $column = 'column';
        $order = 'asc';

        $abstractRepository = $this->getMockAbstractRepositoryWithMockedMethods('getSortableColumns');
        $this->methodWillReturnTrue($abstractRepository, 'getSortableColumns');
        $abstractRepository->setSortingOptions($column, $order);

        $sortCriteria = new SortCriteria($column, $order);
        $this->assertTrue($abstractRepository->getCriteria()->contains($sortCriteria));
    }

    /**
     *
     */
    public function testGetRelations()
    {
        $this->methodWillReturnTrue($this->model, '_getRelations');
        $this->assertTrue($this->abstractRepository->getRelations());
    }


    /**
     *
     */
    public function testSaveAssociated()
    {
        $data = [];
        $options = [];
        $model = null;

        $this->methodWillReturnTrue($this->model, 'saveAssociated', [$data, $options, $model]);
        $this->assertTrue($this->abstractRepository->saveAssociated($data, $options, $model));
    }

    /**
     *
     */
    public function testCreate()
    {
        $data = [];

        $this->methodWillReturnTrue($this->model, 'create', [$data]);
        $this->assertTrue($this->abstractRepository->create($data));
    }

    /**
     *
     */
    public function testCreateWith()
    {
        $data = [];
        $field = 'field';
        $value = 'value';

        $abstractRepository = $this->getMockAbstractRepositoryWithMockedMethods('create');
        $this->methodWillReturnTrue($abstractRepository, 'create', [array_merge($data, ['field' => 'value'])]);
        $this->assertTrue($abstractRepository->createWith($data, $field, $value));
    }

    /**
     *
     */
    public function testIncrementApplyCriteria()
    {
        $column = 'column';
        $value = 'value';

        $this->expectCallMethod($this->abstractRepository, 'applyCriteria');
        $this->abstractRepository->increment($column, $value);
    }

    /**
     *
     */
    public function testIncrement()
    {
        $column = 'column';
        $value = 'value';

        $this->methodWillReturnTrue($this->modelQuery, 'increment', [$column, $value]);
        $this->assertTrue($this->abstractRepository->increment($column, $value));
    }

    /**
     *
     */
    public function testDecrementApplyCriteria()
    {
        $column = 'column';
        $value = 'value';

        $this->expectCallMethod($this->abstractRepository, 'applyCriteria');
        $this->abstractRepository->decrement($column, $value);
    }

    /**
     *
     */
    public function testDecrement()
    {
        $column = 'column';
        $value = 'value';

        $this->methodWillReturnTrue($this->modelQuery, 'decrement', [$column, $value]);
        $this->assertTrue($this->abstractRepository->decrement($column, $value));
    }

    /**
     *
     */
    public function testUpdate()
    {
        $id = 1;
        $attribute = '_id';
        $data = ['attribute' => 'value'];

        $abstractRepository = $this->getMockAbstractRepositoryWithMockedMethods(['getKeyName']);
        $this->setProtectedAttributeOf($abstractRepository, 'modelQuery', $this->modelQuery);

        $this->methodWillReturn($abstractRepository , 'getKeyName', $attribute);
        $this->methodWillReturnTrue($this->modelQuery, 'update', [$data]);
        $this->assertTrue($abstractRepository ->update($data, $id, $attribute));

        $whereCriteria = new WhereCriteria($attribute, $id);
        $this->assertTrue($abstractRepository ->getCriteria()->contains($whereCriteria));
    }

    /**
     *
     */
    public function testUpdateApplyCriteria()
    {
        $id = 1;
        $attribute = 'id';
        $data = ['attribute' => 'value'];

        $this->expectCallMethod($this->abstractRepository, 'applyCriteria');
        $this->abstractRepository->update($data, $id, $attribute);
    }

    public function testUpdateBased()
    {
        //TODO
        $this->assertTrue(true);
    }

    /**
     *
     */
    public function testDestroyApplyCriteria()
    {
        $abstractRepository = $this->getMockAbstractRepositoryWithMockedMethods('getKeyName');
        $this->expectCallMethod($abstractRepository, 'applyCriteria');
        $abstractRepository->destroy(1);
    }

    /**
     *
     */
    public function testDestroy()
    {
        $pk = 'id';
        $abstractRepository = $this->getMockAbstractRepositoryWithMockedMethods('getKeyName');

        $this->methodWillReturn($abstractRepository, 'getKeyName', $pk);
        $this->setProtectedAttributeOf($abstractRepository, 'modelQuery', $this->modelQuery);
        $this->methodWillReturnTrue($this->modelQuery, 'delete');
        $this->assertTrue($abstractRepository->destroy(1));

        $whereCriteria = new WhereCriteria($pk, 1);
        $this->assertTrue($abstractRepository->getCriteria()->contains($whereCriteria));
    }

    /**
     *
     */
    public function testDelete()
    {
        $abstractRepository = $this->getMockAbstractRepositoryWithMockedMethods('destroy');
        $this->methodWillReturnTrue($abstractRepository, 'destroy', [1]);
        $this->assertTrue($abstractRepository->delete(1));
    }

    /**
     *
     */
    public function testDestroyBy_whenModelIsNotEmpty()
    {
        $this->methodWillReturnTrue($this->modelQuery, 'delete');
        $this->assertTrue($this->abstractRepository->destroyBy('column', 'value'));

        $whereCriteria = new WhereCriteria('column', 'value');
        $this->assertTrue($this->abstractRepository->getCriteria()->contains($whereCriteria));

        $limitCriteria = new LimitCriteria(1);
        $this->assertTrue($this->abstractRepository->getCriteria()->contains($limitCriteria));
    }

    /**
     *
     */
    public function testAll_FixSelectedColumns()
    {
        $this->expectCallMethodWithArgument($this->abstractRepository, 'fixSelectedColumns', [null]);
        $this->abstractRepository->all();
    }

    /**
     *
     */
    public function testAll_ApplyCriteria()
    {
        $this->expectCallMethod($this->abstractRepository, 'applyCriteria');
        $this->abstractRepository->all();
    }

    /**
     *
     */
    public function testAll_checkReturned()
    {
        $this->methodWillReturnTrue($this->modelQuery, 'get');
        $this->assertTrue($this->abstractRepository->all());
    }

    /**
     *
     */
    public function testFirst_FixSelectedColumns()
    {
        $this->expectCallMethodWithArgument($this->abstractRepository, 'fixSelectedColumns', [null]);
        $this->abstractRepository->first();
    }

    /**
     *
     */
    public function testFirst_ApplyCriteria()
    {
        $this->expectCallMethod($this->abstractRepository, 'applyCriteria');
        $this->abstractRepository->first();
    }

    /**
     *
     */
    public function testFirst_checkReturned()
    {
        $this->methodWillReturnTrue($this->modelQuery, 'first');
        $this->assertTrue($this->abstractRepository->first());
    }

    /**
     *
     */
    public function testLastApplyCriteria()
    {
        $columns = [];
        $abstractRepository = $this->getMockAbstractRepositoryWithMockedMethods();
        $this->expectCallMethodWithArgument($abstractRepository, 'fixSelectedColumns', [$columns]);
        $abstractRepository->first($columns);
    }

    /**
     *
     */
    public function testLast()
    {
        $columns = 'columns';

        $abstractRepository = $this->getMockAbstractRepositoryWithMockedMethods(['first', 'getKeyName']);
        $this->methodWillReturn($abstractRepository, 'getKeyName', 'id');
        $this->methodWillReturnTrue($abstractRepository, 'first', [$columns]);
        $this->assertTrue($abstractRepository->last($columns));

        $selectCriteria = new SortCriteria('id', 'desc');
        $this->assertTrue($abstractRepository->getCriteria()->contains($selectCriteria));
    }

    /**
     *
     */
    public function testFind_FixSelectedColumns()
    {
        $this->expectCallMethodWithArgument($this->abstractRepository, 'fixSelectedColumns', [null]);
        $this->abstractRepository->find(1);
    }

    /**
     *
     */
    public function testFind_ApplyCriteria()
    {
        $this->expectCallMethod($this->abstractRepository, 'applyCriteria');
        $this->abstractRepository->find(1);
    }

    /**
     *
     */
    public function testFind_checkReturned()
    {
        $this->methodWillReturnTrue($this->modelQuery, 'find', [1]);
        $this->assertTrue($this->abstractRepository->find(1));
    }


    /**
     *
     */
    public function testFindForShow_WhenEmptyColumns()
    {
        $columns = ['columns'];

        $abstractRepository = $this->getMockAbstractRepositoryWithMockedMethods('find');
        $this->methodWillReturnTrue($abstractRepository, 'find', [1, $columns]);
        $this->assertTrue($abstractRepository->findForShow(1, $columns));
    }

    /**
     *
     */
    public function testFindForShow_WhenIsNotEmptyColumns()
    {
        $columns = ['columns'];

        $abstractRepository = $this->getMockAbstractRepositoryWithMockedMethods('find');
        $model = $this->getMockObjectWithMockedMethods('getShowAble');
        $this->methodWillReturn($model, 'getShowAble', $columns);
        $this->setProtectedAttributeOf($abstractRepository, 'model', $model);
        $this->methodWillReturnTrue($abstractRepository, 'find', [1, $columns]);

        $this->assertTrue($abstractRepository->findForShow(1));
    }

    /**
     *
     */
    public function testFindBy()
    {
        $this->methodsWillReturnTrue($this->modelQuery, 'first');
        $this->assertTrue($this->abstractRepository->findBy('attribute', 'value'));
    }

    /**
     *
     */
    public function testFindByCheck()
    {
        //TODO
        $this->assertTrue(true);
    }

    /**
     *
     */
    public function testFindAllBy()
    {
        $this->methodsWillReturnTrue($this->modelQuery, 'get');
        $this->assertTrue($this->abstractRepository->findAllBy('attribute', 'value'));
    }

    /**
     *
     */
    public function testFindAllByCheck()
    {
        //TODO
        $this->assertTrue(true);
    }


    /**
     *
     */
    public function testFndField_WhenData_has_not_Attribute_key()
    {
        $abstractRepository = $this->getMockAbstractRepositoryWithMockedMethods('find');
        $this->assertFalse($abstractRepository->findAttribute('id', 'attribute'));
    }

    /**
     *
     */
    public function testFindField_WhenData_has_Attribute_key()
    {
        $attribute = 'attribute';
        $data[$attribute] = 'value';
        $expected = $data[$attribute];

        $abstractRepository = $this->getMockAbstractRepositoryWithMockedMethods('find');
        $this->methodWillReturn($abstractRepository, 'find', $data);
        $this->assertEquals($expected, $abstractRepository->findAttribute('id', $attribute));
    }

    /**
~     *
     */
    public function testFindFillable()
    {
        $abstractRepository = $this->getMockAbstractRepositoryWithMockedMethods('find');
        $this->methodWillReturnTrue($abstractRepository, 'find', [1, null]);
        $this->assertTrue($abstractRepository->findFillable(1));
    }

    /**
     *
     */
    public function testFillableTODO()
    {
        //TODO
        $this->assertTrue(true);
    }

    /**
     *
     */
    public function testFindFillableWithWhenRelationIsNotEmpty()
    {
        $id = 1;
        $related = [
            'relation' => [
                'columns' => [
                    'id'
                ]
            ]
        ];

        $abstractRepository = $this->getMockAbstractRepositoryWithMockedMethods('findFillable');
        $this->methodWillReturnTrue($abstractRepository, 'findFillable', [1]);
        $this->assertTrue($abstractRepository->findFillableWith($id, $related));

        $relationCriteria = new RelationCriteria($related);
        $this->assertTrue($abstractRepository->getCriteria()->contains($relationCriteria));
    }

    /**
     *
     */
    public function testFindFillableWithWhenRelationIsEmpty()
    {
        $id = 1;
        $related = [];

        $abstractRepository = $this->getMockAbstractRepositoryWithMockedMethods('findFillable');
        $this->methodWillReturnTrue($abstractRepository, 'findFillable', [1]);
        $this->assertTrue($abstractRepository->findFillableWith($id, $related));
        $this->assertEmpty($abstractRepository->getCriteria());
    }

    /**
     *
     */
    public function testFindFillableWhere()
    {
        $id = 1;
        $field = 'field';
        $value = 'value';
        $cmp = '=';

        $abstractRepository = $this->getMockAbstractRepositoryWithMockedMethods('findFillable');
        $this->methodWillReturnTrue($abstractRepository, 'findFillable', [$id]);
        $this->assertTrue($abstractRepository->findFillableWhere($id, $field, $value, $cmp));

        $whereCriteria = new WhereCriteria($field, $value, $cmp);
        $this->assertTrue($abstractRepository->getCriteria()->contains($whereCriteria));
    }

    /**
     *
     */
    public function testFindList()
    {
        $this->assertTrue(true);
//        $listable = [
//            'columns' => [
//                'col'
//            ],
//            'value' => 'value',
//            'key' => 'key'
//        ];
//        $expected = [
//            [$listable['columns'], null, null],
//            $listable['value'],
//            $listable['key']
//        ];
//        $methods = ['all', 'pluck', 'all'];
//
//        $abstractRepository = $this->getMockAbstractRepositoryWithMockedMethods(['all', 'fixColumns']);
//        $this->chainMethodsWillReturnArguments($methods, $abstractRepository);
//        $this->methodWillReturnArguments('fixColumns', $abstractRepository);
//        $this->assertEquals($expected, $abstractRepository->findList(false, $listable));
    }

    /**
     *
     */
    public function testFindListCheckCriteria()
    {
        $this->assertTrue(true);
//        $listable = [
//            'columns' => [
//                'col'
//            ],
//            'value' => 'value',
//            'key' => 'key',
//            'relations' => [
//                'relation' => [
//                    'columns' => ['column']
//                ]
//            ]
//        ];
//        $expected = [
//            [$listable['columns'], null, null],
//            $listable['value'],
//            $listable['key']
//        ];
//        $methods = ['all', 'pluck', 'all'];
//
//        $abstractRepository = $this->getMockAbstractRepositoryWithMockedMethods(['getListableColumns','all', 'fixColumns']);
//        $this->chainMethodsWillReturnArguments($methods, $abstractRepository);
//        $this->methodWillReturnArguments('fixColumns', $abstractRepository);
//        $this->methodWillReturn($listable, 'getListableColumns', $abstractRepository);
//        $this->assertEquals($expected, $abstractRepository->findList());
//
//        $activeCriteria = new ActiveCriteria();
//        $relationCriteria = new RelationCriteria($listable['relations']);
//        $this->assertTrue($abstractRepository->getCriteria()->contains($activeCriteria));
//        $this->assertTrue($abstractRepository->getCriteria()->contains($relationCriteria));
    }

    /**
     *
     */
    public function testFindListBy()
    {
        $attribute = 'attribute';
        $value = 'value';

        $abstractRepository = $this->getMockAbstractRepositoryWithMockedMethods(['findList']);
        $this->methodWillReturnTrue($abstractRepository, 'findList', [true, null]);
        $this->assertTrue($abstractRepository->findListBy($attribute , $value));

        $whereCriteria = new WhereCriteria($attribute, $value);
        $this->assertTrue($abstractRepository->getCriteria()->contains($whereCriteria));
    }

    /**
     *
     */
    public function testPaginate()
    {
        $perPage = 15;
        $columns = ['col'];
        $group = 'list';

        $abstractRepository = $this->getMockAbstractRepositoryWithMockedMethods(['fixColumns']);
        $this->setProtectedAttributeOf($abstractRepository, 'modelQuery', $this->modelQuery);
        $this->methodWillReturnTrue($abstractRepository, 'fixColumns', [$columns]);
        $this->methodWillReturnTrue($this->modelQuery, 'paginate', [$perPage, true]);

        $this->assertTrue($abstractRepository->paginate($perPage, $columns, $group));
    }

    /**
     *
     */
    public function testPaginateWhenColumnsIsEmpty() {
        $perPage = 15;
        $group = 'list';

        $abstractRepository = $this->getMockAbstractRepositoryWithMockedMethods(['fixColumns' ,'getIndexableColumns']);
        $this->setProtectedAttributeOf($abstractRepository, 'modelQuery', $this->modelQuery);
        $this->methodWillReturnTrue($abstractRepository, 'getIndexableColumns', [false, true, $group]);
        $this->methodWillReturnTrue($abstractRepository, 'fixColumns', [true]);
        $this->methodWillReturnTrue($this->modelQuery, 'paginate', [$perPage, true]);

        $this->assertTrue($abstractRepository->paginate($perPage, null, $group));
    }

    /**
     *
     */
    public function testPaginateWhere()
    {
        $field = '';
        $value = '';
        $cmp = '=';

        $abstractRepository = $this->getMockAbstractRepositoryWithMockedMethods('paginate');
        $this->methodsWillReturnTrue($abstractRepository, 'paginate');
        $this->assertTrue($abstractRepository->paginateWhere($field, $value, $cmp));

        $whereCriteria = new WhereCriteria($field, $value, $cmp);
        $this->assertTrue($abstractRepository->getCriteria()->contains($whereCriteria));
    }

    /**
     *
     */
    public function testFindCountWhenEmptyAttributeOrValue()
    {
        $this->methodsWillReturnTrue($this->modelQuery, 'count');
        $this->assertTrue($this->abstractRepository->findCount());
        $this->assertEmpty($this->abstractRepository->getCriteria());
    }

    /**
     *
     */
    public function testFindCountWhenIsNotEmptyAttributeOrValueAndAttributeIsArray()
    {
        $attribute = 'attribute';
        $value = ['value'];
        $cmp = '=';

        $this->methodsWillReturnTrue($this->modelQuery, 'count');
        $this->assertTrue($this->abstractRepository->findCount($attribute, $value, $cmp));

        $whereInCriteria = new WhereInCriteria($attribute, $value);
        $selectCriteria = new SelectCriteria($attribute);
        $this->assertTrue($this->abstractRepository->getCriteria()->contains($whereInCriteria));
        $this->assertTrue($this->abstractRepository->getCriteria()->contains($selectCriteria));

    }

    /**
     *
     */
    public function testFindCountWhenIsNotEmptyAttributeOrValueAndAttributeIsNotArray()
    {
        $attribute = 'attribute';
        $value = 'value';
        $cmp = '=';

        $this->methodsWillReturnTrue($this->modelQuery, 'count');
        $this->assertTrue($this->abstractRepository->findCount($attribute, $value, $cmp));

        $whereCriteria = new WhereCriteria($attribute, $value, $cmp);
        $selectCriteria = new SelectCriteria($attribute);
        $this->assertTrue($this->abstractRepository->getCriteria()->contains($whereCriteria));
        $this->assertTrue($this->abstractRepository->getCriteria()->contains($selectCriteria));
    }

    /**
     *
     */
    public function testExists()
    {
        $id = 1;
        $abstractRepository = $this->getMockAbstractRepositoryWithMockedMethods(['existsWhere', 'getKeyName']);
        $this->methodWillReturn($abstractRepository, 'getKeyName', 'id');
        $this->methodWillReturnTrue($abstractRepository, 'existsWhere', ['id', $id]);
        $this->assertTrue($abstractRepository->exists($id));
    }

    /**
     *
     */
    public function testExistsWhereWhenFindCountGraterThenIsZero()
    {
        $attribute = 'attribute';
        $value = 'value';

        $abstractRepository = $this->getMockAbstractRepositoryWithMockedMethods('findCount');
        $this->methodWillReturn($abstractRepository, 'findCount', 1);
        $this->assertTrue($abstractRepository->existsWhere($attribute, $value));

        $whereCriteria = new WhereCriteria($attribute, $value);
        $this->assertTrue($abstractRepository->getCriteria()->contains($whereCriteria));
    }

    /**
     *
     */
    public function testExistsWhereWhenFindCountSmallerThenIsZero()
    {
        $attribute = 'attribute';
        $value = 'value';

        $abstractRepository = $this->getMockAbstractRepositoryWithMockedMethods('findCount');
        $this->methodWillReturn($abstractRepository, 'findCount', 0);
        $this->assertFalse($abstractRepository->existsWhere($attribute, $value));

        $whereCriteria = new WhereCriteria($attribute, $value);
        $this->assertTrue($abstractRepository->getCriteria()->contains($whereCriteria));
    }

    /**
     *
     */
    protected function findByCriteria()
    {
        //TODO
        $this->assertTrue(true);
    }

    public function testFixSelectedColumns()
    {
        //TODO
        $this->assertTrue(true);
    }

    /**
     *
     */
    public function testGetCriteria()
    {
        $this->setProtectedAttributeOf($this->abstractRepository, 'criteria', 'criteria');
        $this->assertEquals('criteria', $this->abstractRepository->getCriteria());
    }

    /**
     *
     */
    public function testGetByCriteria()
    {
        $criteria = $this->getMockForAbstract(Criteria::class, [], ['apply']);

        $this->methodWillReturnTrue($criteria, 'apply', [$this->modelQuery, $this->abstractRepository]);
        $this->assertInstanceOf(AbstractRepository::class, $this->abstractRepository->getByCriteria($criteria));

        $this->assertTrue($this->getProtectedAttributeOf($this->abstractRepository, 'modelQuery'));
    }

    /**
     *
     */
    public function testResetScope()
    {
        $this->assertInstanceOf(AbstractRepository::class, $this->abstractRepository->resetScope());
    }

    /**
     *
     */
    public function testResetScopeTODO()
    {
        //TODO
        $this->assertInstanceOf(AbstractRepository::class, $this->abstractRepository->resetScope());
    }

    /**
     *
     */
    public function testPushCriteriaWhenContainsIsFalse()
    {
        $criteria = $this->getMockForAbstract(Criteria::class, [], ['contains']);
        $this->methodWillReturnTrue($criteria, 'contains');
        $this->setProtectedAttributeOf($this->abstractRepository, 'criteria', $criteria);
        $this->assertInstanceOf(AbstractRepository::class, $this->abstractRepository->pushCriteria($criteria));
    }

    /**
     *
     */
    public function testPushCriteriaWhenContainsIsTrue()
    {
        //TODO
        $criteria = $this->getMockForAbstract(Criteria::class, [], ['contains', 'push']);
        $this->methodWillReturnFalse($criteria, 'contains');
        $this->setProtectedAttributeOf($this->abstractRepository, 'criteria', $criteria);
        $this->assertInstanceOf(AbstractRepository::class, $this->abstractRepository->pushCriteria($criteria));
    }

    /**
     *
     */
    public function testSkipCriteria()
    {
        $this->abstractRepository->skipCriteria(false);
        $this->assertFalse($this->getProtectedAttributeOf($this->abstractRepository, 'skipCriteria'));
    }

    /**
     *
     */
    public function testApplyCriteria()
    {
        $abstractRepository = $this->getMockAbstractRepositoryWithMockedMethods('getCriteria', false);
        $abstractRepository->skipCriteria(true);
        $this->assertInstanceOf(AbstractRepository::class, $abstractRepository->applyCriteria());
    }

    public function testApplyCriteriaTODO()
    {
        //TODO
        $abstractRepository = $this->getMockAbstractRepositoryWithMockedMethods('getCriteria', false);
        $abstractRepository->skipCriteria(true);
        $this->assertInstanceOf(AbstractRepository::class, $abstractRepository->applyCriteria());
    }

    /**
     *
     */
    public function testStartTransaction()
    {
        DB::shouldReceive('beginTransaction')
            ->once()
            ->andReturn(true);
        $this->assertTrue($this->abstractRepository->startTransaction());
    }

    /**
     *
     */
    public function testCommitTransaction()
    {
        DB::shouldReceive('commit')
            ->once()
            ->andReturn(true);
        $this->assertTrue($this->abstractRepository->commitTransaction());
    }

    /**
     *
     */
    public function testRollbackTransaction() {
        DB::shouldReceive('rollBack')
            ->once()
            ->andReturn(true);
        $this->assertTrue($this->abstractRepository->rollbackTransaction());
    }



    /**
     *
     */
    private function setModelInAbstractRepository()
    {
        $this->model = $this->getMockObjectWithMockedMethods([
            'getTable',
            'getKeyName',
            'getFillable',
            'getIndexable',
            'getShowable',
            'getSearchable',
            'getListable',
            'getSortable',
            'getStatusColumn',
            '_getRelations',
            'saveAssociated',
            'create',
        ]);
        $this->setProtectedAttributeOf($this->abstractRepository, 'model', $this->model);
    }

    /**
     *
     */
    private function setModelQueryInAbstractRepository()
    {
        $this->modelQuery = $this->getMockObjectWithMockedMethods([
            'increment',
            'decrement',
            'where',
            'get',
            'update',
            'first',
            'find',
            'paginate',
            'count',
            'delete'
        ]);
        $this->setProtectedAttributeOf($this->abstractRepository, 'modelQuery', $this->modelQuery);
    }

    /**
     * @param array $methods
     * @param bool $mockApplyCriteria
     * @return mixed
     */
    private function getMockAbstractRepositoryWithMockedMethods($methods = [], $mockApplyCriteria = true)
    {
        make_array($methods);
        $methods[] = 'makeModel';
        $methods[] = 'fixSelectedColumns';

        if ($mockApplyCriteria) {
            $methods[] = 'applyCriteria';
        }

        return $this->getMockForAbstract(
            AbstractRepository::class,
            [app(Collection::class)],
            $methods
        );
    }

}
