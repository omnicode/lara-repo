<?php

namespace Tests\Eloquent;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\DB;
use LaraRepo\Criteria\Criteria;
use LaraRepo\Criteria\Order\SortCriteria;
use LaraRepo\Criteria\Select\SelectCriteria;
use LaraRepo\Criteria\Where\ActiveCriteria;
use LaraRepo\Criteria\Where\WhereCriteria;
use LaraRepo\Criteria\Where\WhereInCriteria;
use LaraRepo\Criteria\With\RelationCriteria;
use LaraRepo\Eloquent\AbstractRepository;
use LaraRepo\Exceptions\RepositoryException;
use LaraTest\Traits\AccessProtectedTraits;
use LaraTest\Traits\AssertionTraits;
use LaraTest\Traits\MockTraits;
use LaraTools\Utility\LaraUtil;

class AbstractRepositoryTest extends \TestCase
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
        $this->methodWillReturnTrue('modelClass', $this->abstractRepository);
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
        $this->methodWillReturn('modelQuery', 'newQuery', $model);
        $this->methodWillReturn(Model::class, 'modelClass', $abstractRepository);

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

        $abstractRepository->expects($this->any())->method('modelClass')->willReturn('model');
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
        $this->methodWillReturn('modelQuery', 'newQuery', $model);
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
        $this->methodWillReturnTrue('getTable', $this->model);
        $this->assertTrue($this->abstractRepository->getTable());
    }

    /**
     *
     */
    public function testGetKeyName()
    {
        $this->methodWillReturnTrue('getKeyName', $this->model);
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

    /**
     *
     */
    public function testFixColumnsWhenTableIsEmptyEnd()
    {
        $columns = ['col'];
        $abstractRepository = $this->getMockAbstractRepositoryWithMockedMethods(['getTable']);
        $this->methodWillReturn('table', 'getTable', $abstractRepository);
        $this->assertEquals(['table.col'], $abstractRepository->fixColumns($columns));
    }

    /**
     *
     */
    public function testFixColumnsWhenTableIsNotEmpty()
    {
        $table = 'table';
        $columns = ['column'];
        $expected = [$table. '.column'];
        $abstractRepository = $this->getMockAbstractRepositoryWithMockedMethods(['getTable']);
        $this->assertEquals($expected, $abstractRepository->fixColumns($columns, $table));
    }

    /**
     *
     */
    public function testGetFillableColumns()
    {
        $this->methodWillReturnTrue('getFillable', $this->model);
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
        $expected = [$full, $hidden, $group];

        $this->methodWillReturnArguments('getIndexable', $this->model);
        $this->assertEquals($expected, $this->abstractRepository->getIndexableColumns($full, $hidden, $group));
    }

    /**
     *
     */
    public function testGetShowableColumns()
    {
        $full = false;
        $hidden = true;
        $group = 'list';
        $expected = [$full, $hidden, $group];

        $this->methodWillReturnArguments('getShowable', $this->model);
        $this->assertEquals($expected, $this->abstractRepository->getShowableColumns($full, $hidden, $group));
    }

    /**
     *
     */
    public function testGetSearchableColumns()
    {
        $this->methodWillReturnTrue('getSearchable', $this->model);
        $this->assertTrue($this->abstractRepository->getSearchableColumns());
    }

    /**
     *
     */
    public function testGetListableColumns()
    {
        $this->methodWillReturnTrue('getListable', $this->model);
        $this->assertTrue($this->abstractRepository->getListableColumns());
    }

    /**
     *
     */
    public function testGetSortableColumns()
    {
        $column = 'column';
        $group = 'list';
        $expected = [$column, $group];

        $this->methodWillReturnArguments('getSortable', $this->model);
        $this->assertEquals($expected, $this->abstractRepository->getSortableColumns($column, $group));
    }

    /**
     *
     */
    public function testGetStatusColumn()
    {
        $this->methodWillReturnTrue('getStatusColumn', $this->model);
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
        $this->methodWillReturnTrue('getSortableColumns', $abstractRepository);
        $abstractRepository->setSortingOptions($column, $order);

        $sortCriteria = new SortCriteria($column, $order);
        $this->assertTrue($abstractRepository->getCriteria()->contains($sortCriteria));
    }

    /**
     *
     */
    public function testGetRelations()
    {
        $this->methodWillReturnTrue('_getRelations', $this->model);
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
        $expected = [$data, $options, $model];

        $this->methodWillReturnArguments('saveAssociated', $this->model);
        $this->assertEquals($expected, $this->abstractRepository->saveAssociated($data, $options, $model));
    }

    /**
     *
     */
    public function testCreate()
    {
        $data = [];
        $expected = [$data];

        $this->methodWillReturnArguments('create', $this->model);
        $this->assertEquals($expected, $this->abstractRepository->create($data));
    }

    /**
     *
     */
    public function testCreateWith()
    {
        $data = [];
        $field = 'field';
        $value = 'value';
        $expected = [array_merge($data, ['field' => 'value'])];

        $abstractRepository = $this->getMockAbstractRepositoryWithMockedMethods('create');
        $this->methodWillReturnArguments('create', $abstractRepository);
        $this->assertEquals($expected, $abstractRepository->createWith($data, $field, $value));
    }

    /**
     *
     */
    public function testIncrement()
    {
        $column = 'column';
        $value = 'value';
        $expected = [$column, $value];

        $this->methodWillReturnArguments('increment', $this->modelQuery);
        $this->assertEquals($expected, $this->abstractRepository->increment($column, $value));
    }

    /**
     *
     */
    public function testIncrementPushCriteria()
    {
        $column = 'column';
        $value = 'value';
        $this->expectCallMethod($this->abstractRepository, 'applyCriteria');
        $this->abstractRepository->increment($column, $value);
    }

    /**
     *
     */
    public function testUpdate()
    {
        $id = 1;
        $attribute = 'id';
        $data = ['attribute' => 'value'];

        $this->methodWillReturnArguments('update', $this->modelQuery);
        $this->assertEquals([$data], $this->abstractRepository->update($data, $id, $attribute));

        $whereCriteria = new WhereCriteria($attribute, $id);
        $this->assertTrue($this->abstractRepository->getCriteria()->contains($whereCriteria));
    }

    /**
     *
     */
    public function testUpdatePushCriteriaApplyCriteria()
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
    }


    /**
     *
     */
    public function testDestroy_whenModelIsNotEmpty()
    {
        $abstractRepository = $this->getMockAbstractRepositoryWithMockedMethods('find');
        $model = $this->getMockObjectWithMockedMethods('delete');
        $this->methodsWillReturnTrue('delete', $model);
        $this->methodWillReturn($model, 'find', $abstractRepository);
        $this->assertTrue($abstractRepository->destroy(1));
    }

    /**
     *
     */
    public function testDestroy_whenModelIsEmpty()
    {
        $abstractRepository = $this->getMockAbstractRepositoryWithMockedMethods('find');
        $this->assertFalse($abstractRepository->destroy(1));
    }

    /**
     *
     */
    public function testDestroyBy_whenModelIsNotEmpty()
    {
        $abstractRepository = $this->getMockAbstractRepositoryWithMockedMethods('findBy');
        $model = $this->getMockObjectWithMockedMethods('delete');
        $this->methodsWillReturnTrue('delete', $model);
        $this->methodWillReturn($model, 'findBy', $abstractRepository);
        $this->assertTrue($abstractRepository->destroyBy('column', 'value'));
    }

    /**
     *
     */
    public function testDestroyBy_whenModelIsEmpty()
    {
        $abstractRepository = $this->getMockAbstractRepositoryWithMockedMethods('findBy');
        $this->assertFalse($abstractRepository->destroyBy('column', 'value'));
    }

    /**
     *
     */
    public function testAll()
    {
        $this->methodsWillReturnTrue('get', $this->modelQuery);
        $this->assertTrue($this->abstractRepository->all());
    }

    /**
     *
     */
    public function testAllCheckCriteria()
    {
        $columns = ['col'];
        $this->methodsWillReturnTrue('get', $this->modelQuery);
        $this->assertTrue($this->abstractRepository->all($columns));
    }

    /**
     *
     */
    public function testAllCheckNotHasCriteria()
    {
        //TODO
    }

    /**
     *
     */
    public function testFirst()
    {
        $this->methodsWillReturnTrue('first', $this->modelQuery);
        $this->assertTrue($this->abstractRepository->first());
    }

    /**
     *
     */
    public function testFirstCheckCriteria()
    {
        $columns = ['col'];
        $this->methodsWillReturnTrue('first', $this->modelQuery);
        $selectCriteria = new SelectCriteria($columns);
        $this->assertTrue($this->abstractRepository->first($columns));
        $this->assertTrue($this->abstractRepository->getCriteria()->contains($selectCriteria));
    }

    /**
     *
     */
    public function testFirstCheckNotHasCriteria()
    {
        //TODO
    }

    /**
     *
     */
    public function testLast_WhenColumnIsArray()
    {
        $columns = [];
        $abstractRepository = $this->getMockAbstractRepositoryWithMockedMethods('first');
        $this->methodWillReturnArguments('first', $abstractRepository);
        $this->assertEquals([$columns], $abstractRepository->first($columns));
    }

    /**
     *
     */
    public function testLast_WhenColumnIsNotArray()
    {
        $columns = 'columns';
        $correctedColumns = [$columns];
        $abstractRepository = $this->getMockAbstractRepositoryWithMockedMethods('first');
        $this->methodWillReturnArguments('first', $abstractRepository);
        $selectCriteria = new SelectCriteria($correctedColumns);
        $this->assertEquals($correctedColumns, $abstractRepository->first($columns));
        $this->assertFalse($this->abstractRepository->getCriteria()->contains($selectCriteria));
    }

    /**
     *
     */
    public function testFind()
    {
        $this->methodWillReturnArguments('find', $this->modelQuery);
        $this->assertEquals([1], $this->abstractRepository->find(1));
    }

    /**
     *
     */
    public function testFindCheckCriteria()
    {
        //TODO
    }

    /**
     *
     */
    public function testFindForShow_WhenEmptyColumns()
    {
        $columns = ['columns'];

        $abstractRepository = $this->getMockAbstractRepositoryWithMockedMethods('find');
        $this->methodWillReturnArguments('find',$abstractRepository);
        $this->assertEquals([1, $columns], $abstractRepository->findForShow(1, $columns));
    }

    /**
     *
     */
    public function testFindForShow_WhenIsNotEmptyColumns()
    {
        $columns = ['columns'];
        $expected = [1, $columns];

        $abstractRepository = $this->getMockAbstractRepositoryWithMockedMethods('find');
        $model = $this->getMockObjectWithMockedMethods('getShowAble');
        $this->methodWillReturn($columns, 'getShowAble', $model);
        $this->setProtectedAttributeOf($abstractRepository, 'model', $model);
        $this->methodWillReturnArguments('find',$abstractRepository);

        $this->assertEquals($expected, $abstractRepository->findForShow(1));
    }

    /**
     *
     */
    public function testFindBy()
    {
        $this->methodsWillReturnTrue('first', $this->modelQuery);
        $this->assertTrue($this->abstractRepository->findBy('attribute', 'value'));
    }

    /**
     *
     */
    public function testFindByCheck()
    {
        //TODO
    }

    /**
     *
     */
    public function testFindAllBy()
    {
        $this->methodsWillReturnTrue('get', $this->modelQuery);
        $this->assertTrue($this->abstractRepository->findAllBy('attribute', 'value'));
    }

    /**
     *
     */
    public function testFindAllByCheck()
    {
        //TODO
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
        $this->methodWillReturn($data, 'find', $abstractRepository);
        $this->assertEquals($expected, $abstractRepository->findAttribute('id', $attribute));
    }

    /**
     *
     */
    public function testFindFillable()
    {
        $expected = [1, null];

        $abstractRepository = $this->getMockAbstractRepositoryWithMockedMethods('find');
        $this->methodWillReturnArguments('find', $abstractRepository);
        $this->assertEquals($expected, $abstractRepository->findFillable(1));
    }

    public function testFillableTODO()
    {
        //TODO
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
        $this->methodWillReturnArguments('findFillable', $abstractRepository);
        $this->assertEquals([1], $abstractRepository->findFillableWith($id, $related));

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
        $this->methodWillReturnArguments('findFillable', $abstractRepository);
        $this->assertEquals([1], $abstractRepository->findFillableWith($id, $related));
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
        $expected = [$id];

        $abstractRepository = $this->getMockAbstractRepositoryWithMockedMethods('findFillable');
        $this->methodWillReturnArguments('findFillable', $abstractRepository);
        $this->assertEquals($expected, $abstractRepository->findFillableWhere($id, $field, $value, $cmp));

        $whereCriteria = new WhereCriteria($field, $value, $cmp);
        $this->assertTrue($abstractRepository->getCriteria()->contains($whereCriteria));
    }

    /**
     *
     */
    public function testFindList()
    {
        $listable = [
            'columns' => [
                'col'
            ],
            'value' => 'value',
            'key' => 'key'
        ];
        $expected = [
            [$listable['columns'], null, null],
            $listable['value'],
            $listable['key']
        ];
        $methods = ['all', 'pluck', 'all'];

        $abstractRepository = $this->getMockAbstractRepositoryWithMockedMethods(['all', 'fixColumns']);
        $this->chainMethodsWillReturnArguments($methods, $abstractRepository);
        $this->methodWillReturnArguments('fixColumns', $abstractRepository);
        $this->assertEquals($expected, $abstractRepository->findList(false, $listable));
    }

    /**
     *
     */
    public function testFindListCheckCriteria()
    {
        $listable = [
            'columns' => [
                'col'
            ],
            'value' => 'value',
            'key' => 'key',
            'relations' => [
                'relation' => [
                    'columns' => ['column']
                ]
            ]
        ];
        $expected = [
            [$listable['columns'], null, null],
            $listable['value'],
            $listable['key']
        ];
        $methods = ['all', 'pluck', 'all'];

        $abstractRepository = $this->getMockAbstractRepositoryWithMockedMethods(['getListableColumns','all', 'fixColumns']);
        $this->chainMethodsWillReturnArguments($methods, $abstractRepository);
        $this->methodWillReturnArguments('fixColumns', $abstractRepository);
        $this->methodWillReturn($listable, 'getListableColumns', $abstractRepository);
        $this->assertEquals($expected, $abstractRepository->findList());

        $activeCriteria = new ActiveCriteria();
        $relationCriteria = new RelationCriteria($listable['relations']);
        $this->assertTrue($abstractRepository->getCriteria()->contains($activeCriteria));
        $this->assertTrue($abstractRepository->getCriteria()->contains($relationCriteria));
    }

    /**
     *
     */
    public function testFindListBy()
    {
        $attribute = 'attribute';
        $value = 'value';
        $expected = [true, null];

        $abstractRepository = $this->getMockAbstractRepositoryWithMockedMethods(['findList']);
        $this->methodWillReturnArguments('findList', $abstractRepository);
        $this->assertEquals($expected, $abstractRepository->findListBy($attribute , $value));

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
        $expected = [$perPage, [$columns, null, null]];

        $abstractRepository = $this->getMockAbstractRepositoryWithMockedMethods(['fixColumns']);
        $this->setProtectedAttributeOf($abstractRepository, 'modelQuery', $this->modelQuery);
        $this->methodWillReturnArguments('fixColumns', $abstractRepository);
        $this->methodWillReturnArguments('paginate', $this->modelQuery);

        $this->assertEquals($expected, $abstractRepository->paginate($perPage, $columns, $group));
    }

    /**
     *
     */
    public function testPaginateWhenColumnsIsEmpty() {
        $perPage = 15;
        $group = 'list';
        $expected = [$perPage, [[false, true, $group], null, null]];

        $abstractRepository = $this->getMockAbstractRepositoryWithMockedMethods(['fixColumns' ,'getIndexableColumns']);
        $this->setProtectedAttributeOf($abstractRepository, 'modelQuery', $this->modelQuery);
        $this->methodWillReturnArguments('fixColumns', $abstractRepository);
        $this->methodWillReturnArguments('getIndexableColumns', $abstractRepository);
        $this->methodWillReturnArguments('paginate', $this->modelQuery);

        $this->assertEquals($expected, $abstractRepository->paginate($perPage, null, $group));
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
        $this->methodsWillReturnTrue('paginate', $abstractRepository);
        $this->assertTrue($abstractRepository->paginateWhere($field, $value, $cmp));

        $whereCriteria = new WhereCriteria($field, $value, $cmp);
        $this->assertTrue($abstractRepository->getCriteria()->contains($whereCriteria));
    }

    /**
     *
     */
    public function testFindCountWhenEmptyAttributeOrValue()
    {
        $this->methodsWillReturnTrue('count', $this->modelQuery);
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

        $this->methodsWillReturnTrue('count', $this->modelQuery);
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

        $this->methodsWillReturnTrue('count', $this->modelQuery);
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
        $abstractRepository = $this->getMockAbstractRepositoryWithMockedMethods('existsWhere');
        $this->methodWillReturnArguments('existsWhere', $abstractRepository);
        $this->assertEquals(['id', $id], $abstractRepository->exists($id));
    }

    /**
     *
     */
    public function testExistsWhereWhenFindCountGraterThenIsZero()
    {
        $attribute = 'attribute';
        $value = 'value';

        $abstractRepository = $this->getMockAbstractRepositoryWithMockedMethods('findCount');
        $this->methodWillReturn(1, 'findCount', $abstractRepository);
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
        $this->methodWillReturn(0, 'findCount', $abstractRepository);
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
    }

    public function testFixSelectedColumns()
    {
        //TODO
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
        $expected = [$this->modelQuery, $this->abstractRepository];

        $this->methodWillReturnArguments('apply', $criteria);
        $this->assertInstanceOf(AbstractRepository::class, $this->abstractRepository->getByCriteria($criteria));

        $this->assertEquals($expected, $this->getProtectedAttributeOf($this->abstractRepository, 'modelQuery'));
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
        $this->methodWillReturnTrue('contains', $criteria);
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
        $this->methodWillReturnFalse('contains', $criteria);
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
            'where',
            'get',
            'update',
            'first',
            'find',
            'paginate',
            'count'
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
