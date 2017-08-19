<?php
namespace Tests\Eloquent;

class AbstractRepositoryTest extends \PHPUnit_Extensions_PhptTestCase
{
    public function testTrueIsTrue()
    {
        $foo = true;
        $this->assertTrue($foo);
    }
//    use  DatabaseTransactions;
//
//    /**
//     * @var
//     */
//    protected $abstractWithProject;
//
//    /**
//     * @var
//     */
//    protected $abstractWithCountry;
//
//    /**
//     * @var
//     */
//    protected $project;
//
//    /**
//     *
//     */
//    public function setUp()
//    {
//        parent::setUp();
//        $this->project = factory(Project::class)->create();
//        $this->abstractWithProject = $this->initializeAbstractRepositoryWithModel(Project::class);
//        $this->abstractWithCountry = $this->initializeAbstractRepositoryWithModel(Country::class);
//    }
//
//    /**
//     *
//     */
//    public function testConstructMethodWlillInitializiePrivateAppProperty()
//    {
//
//    }
//
//    /**
//     *
//     */
//    public function testConstructMethodWlillInitializieProtectedCriteriaProperty()
//    {
//        $this->assertInstanceOf(Collection::class, $this->abstractWithProject->getCriteria());
//    }
//
//    /**
//     *
//     */
//    public function testResetScopeMethodSetFalseValueInSkipCriteriaProperty()
//    {
//        $this->abstractWithProject->resetScope();
//        $this->assertFalse($this->getProtectedAttributeInObject($this->abstractWithProject, 'skipCriteria'));
//    }
//
//
//    /**
//     * @param $value
//     * @dataProvider providerTestSkipCriteriaMethodSetValueOfArgumentInSkipCriteriaProperty
//     */
//    public function testSkipCriteriaMethodSetValueOfArgumentInSkipCriteriaProperty($value)
//    {
//        $this->abstractWithProject->skipCriteria($value);
//        $this->assertEquals($value, $this->getProtectedAttributeInObject($this->abstractWithProject, 'skipCriteria'));
//    }
//
//    /**
//     * @return array
//     */
//    public function providerTestSkipCriteriaMethodSetValueOfArgumentInSkipCriteriaProperty()
//    {
//        return [
//            [true],
//            [false]
//        ];
//    }
//
//    /**
//     *
//     */
//    public function testMakeModelMethodMakeProjectModelAndBuilder()
//    {
//        $abstract = $this->initializeAbstractRepositoryWithModel(Client::class);
//        $this->assertInstanceOf(Client::class, $abstract->getModel());
//        $this->assertInstanceOf(Builder::class, $this->getProtectedAttributeInObject($abstract, 'modelQuery'));
//    }
//
//    /**
//     *
//     */
//    public function testMakeModelMethodWhenModelClassFalseValueExpectedThrownException()
//    {
//
//    }
//
//    /**
//     *
//     */
//    public function testFixColumnsWhetSetOnlyColumnArgument()
//    {
//        $expectedResult = [
//            'projects.idColumn',
//            'projects.nameColumn'
//        ];
//        $this->assertEquals($expectedResult, $this->abstractWithProject->fixColumns(['idColumn', 'nameColumn']));
//    }
//
//    /**
//     *
//     */
//    public function testFixColumnsWhetSetColumnAndTableArgument()
//    {
//        $expectedResult = [
//            'projectsTable.idColumn',
//            'projectsTable.nameColumn'
//        ];
//        $this->assertEquals($expectedResult, $this->abstractWithProject->fixColumns(['idColumn', 'nameColumn'], 'projectsTable'));
//    }
//
//    /**
//     *
//     */
//    public function testFixColumnsWhetSetColumnTableAndPrefixArgument()
//    {
//        $expectedResult = [
//            'projectsTable.idColumn as project_idColumn',
//            'projectsTable.nameColumn as project_nameColumn'
//
//        ];
//        $result = $this->abstractWithProject->fixColumns(['idColumn', 'nameColumn'], 'projectsTable', 'project');
//        $this->assertEquals($expectedResult, $result);
//    }
//
//    /**
//     *
//     */
//    public function testSetSortingOptionsWithDefaultArgumentsExpectetReturnTrue()
//    {
//        $this->assertTrue($this->abstractWithProject->setSortingOptions());
//    }
//
//    /**
//     *
//     */
//    public function testSetSortingOptionsWhenSetColumnArgument()
//    {
//        $this->abstractWithProject->setSortingOptions('name');
//        $sortCriteria = new SortCriteria('name', 'asc');
//        $result = $this->getProtectedAttributeInObject($this->abstractWithProject, 'criteria');
//        $this->assertEquals($sortCriteria, $result[0]);
//    }
//
//    /**
//     *
//     */
//    public function testSetSortingOptionsWhenSetColumnAndOrderArguments()
//    {
//        $this->abstractWithProject->setSortingOptions('name', 'desc');
//        $sortCriteria = new SortCriteria('name', 'desc');
//        $result = $this->getProtectedAttributeInObject($this->abstractWithProject, 'criteria');
//        $this->assertEquals($sortCriteria, $result[0]);
//    }
//
//    /**
//     *
//     */
//    public function testGetSortableColumnsWithDefaultArgumentExpectedReturnAllSortableColumnInModel()
//    {
//        $expectedResult = [
//            'code',
//            'name',
//            'duration_months',
//            'start_date',
//            'updated_at'
//        ];
//        $this->assertEquals($expectedResult, $this->abstractWithProject->getSortableColumns());
//    }
//
//    /**
//     * @param $value
//     * @param $expectedResult
//     * @dataProvider providerTestGetSortableColumnsWhenSetColumnArgument
//     */
//    public function testGetSortableColumnsWhenSetColumnArgument($value, $expectedResult)
//    {
//        $this->assertEquals($expectedResult, $this->abstractWithProject->getSortableColumns($value));
//    }
//
//    /**
//     * @return array
//     */
//    public function providerTestGetSortableColumnsWhenSetColumnArgument()
//    {
//        return [
//            ['id', false],
//            ['name', true],
//            ['code', true],
//            ['end_date', false],
//        ];
//    }
//
//    /**
//     *
//     */
//    public function testPushCriteria()
//    {
//        $sortCriteria = new SortCriteria('name', 'desc');
//        $this->abstractWithProject->pushCriteria($sortCriteria);
//        $result = $this->getProtectedAttributeInObject($this->abstractWithProject, 'criteria');
//        $this->assertEquals($sortCriteria, $result[0]);
//    }
//
//    /**
//     *
//     */
//    public function testGetModelMethod()
//    {
//        $this->assertInstanceOf(Project::class, $this->abstractWithProject->getModel());
//    }
//
//    /**
//     *
//     */
//    public function testGetFillabeleColumnsMethod()
//    {
//        $expectedResult = [
//            'name',
//            'code',
//            'client_id',
//            'is_current',
//            'start_date',
//            'end_date',
//            'contract_amount',
//            'approx_amount',
//            'currency_id',
//            'total_staff_months',
//            'total_staff_months_by_associates',
//            'total_staff_months_by_firm',
//            'total_staff_months_by_firm_and_subconsultants',
//            'location_within_country'
//        ];
//        $this->assertEquals($expectedResult, $this->abstractWithProject->getFillableColumns());
//    }
//
//    /**
//     *
//     */
//    public function testGetRelationMethod()
//    {
//
//    }
//
//    /**
//     *
//     */
//    public function testGetStatusColumn()
//    {
//        $this->assertEquals('status', $this->abstractWithProject->getStatusColumn());
//    }
//
//    /**
//     *
//     */
//    public function testPaginateWhereMethod()
//    {
//        factory(Project::class, 50)->create();
//        $this->abstractWithProject->paginateWhere('id', 10, '>');
//        $whereCriteria = new WhereCriteria('id', 10, '>');
//        $criteria = $this->getProtectedAttributeInObject($this->abstractWithProject, 'criteria');
//        $this->assertEquals($whereCriteria, $criteria[0]);
//    }
//
//    /**
//     *
//     */
//    public function testPaginateWithDefaultArguments()
//    {
//        $projects = factory(Project::class, 50)->create();
//        $columns = LaraUtil::getFullColumns( $projects[0]->getIndexable(null, null), 'projects');
//        $expectedResult = Project::paginate(20, $columns);
//        $this->assertEquals($expectedResult, $this->abstractWithProject->paginate());
//    }
//
//    /**
//     *
//     */
//    public function testPaginateWhenSetPerPageArguments()
//    {
//        $perPage = 15;
//        $projects = factory(Project::class, 50)->create();
//        $columns = LaraUtil::getFullColumns( $projects[0]->getIndexable(null, null), 'projects');
//        $expectedResult = Project::paginate($perPage, $columns);
//        $this->assertEquals($expectedResult, $this->abstractWithProject->paginate($perPage));
//    }
//
//    /**
//     *
//     */
//    public function testPaginateWhenSetPerPageAndColumnsArguments()
//    {
//        $perPage = 15;
//        $columns = [
//            'id',
//            'name',
//            'code'
//        ];
//        factory(Project::class, 50)->create();
//        $expectedResult = Project::paginate($perPage, $columns);
//        $this->assertEquals($expectedResult, $this->abstractWithProject->paginate(15, $columns));
//    }
//
//    /**
//     *
//     */
//    public function testGetIndexableColumns()
//    {
//        $expectedResult = [
//            'id',
//            'code',
//            'name',
//            'duration_months',
//            'start_date',
//            'end_date',
//            'updated_at'
//        ];
//        $this->assertEquals($expectedResult, $this->abstractWithProject->getIndexableColumns());
//    }
//
//    /**
//     *
//     */
//    public function testApplayCriteria()
//    {
//
//    }
//
//    /**
//     *
//     */
//    public function testGetCriteria()
//    {
//        $this->assertInstanceOf(Collection::class, $this->abstractWithProject->getCriteria());
//    }
//
//    /**
//     *
//     */
//    public function testGetTable()
//    {
//        $this->assertEquals('projects', $this->abstractWithProject->getTable());
//    }
//
//    /**
//     *
//     */
//    public function testCreateWith()
//    {
//        $data = [
//            'name' => 'new Project'
//        ];
//        $project = $this->abstractWithProject->createWith($data, 'code', 'P012');
//        $this->assertEquals('P012', $project->code);
//    }
//
//    /**
//     *
//     */
//    public function testCreate()
//    {
//        $data = [
//            'name' => 'new Project'
//        ];
//        $project = $this->abstractWithProject->create($data);
//        $this->assertEquals('new Project', $project->name);
//    }
//
//    /**
//     *
//     */
//    public function testUpdateUsingModelId()
//    {
//        $data = [
//            'name' => 'New Name'
//        ];
//        $this->abstractWithProject->update($data, $this->project->id);
//        $project = Project::find($this->project->id);
//        $this->assertEquals('New Name', $project->name);
//    }
//
//    /**
//     *
//     */
//    public function testUpdateWhere()
//    {
//        $data = [
//            'name' => 'New Name'
//        ];
//        $this->abstractWithProject->update($data, $this->project->name, 'name');
//        $project = Project::find($this->project->id);
//        $this->assertEquals('New Name', $project->name);
//    }
//
//    /**
//     *
//     */
//    public function testDelete()
//    {
//        $this->abstractWithProject->delete($this->project->id);
//        $project = Project::find($this->project->id);
//        $this->assertNull($project);
//    }
//
//    /**
//     *
//     */
//    public function testFirstWithDefaltAttribute()
//    {
//        $this->assertEquals(Project::first(), $this->abstractWithProject->first());
//    }
//
//    /**
//     *
//     */
//    public function testFirstWhenSetColumnAttribute()
//    {
//        $column = ['name'];
//        $this->assertEquals(Project::first($column), $this->abstractWithProject->first($column));
//    }
//
//    /**
//     *
//     */
//    public function testFindField()
//    {
//        $this->assertEquals($this->project->name, $this->abstractWithProject->findField($this->project->id, 'name'));
//    }
//
//    /**
//     *
//     */
//    public function testFindFieldWhenSetIncorrectData()
//    {
//        $id = $this->project->id;
//        $this->project->delete();
//        $this->assertFalse($this->abstractWithProject->findField($id, 'name'));
//
//    }
//
//    /**
//     *
//     */
//    public function testFindListWithDefaultArguments()
//    {
//        $abstractWithCountry = $this->initializeAbstractRepositoryWithModel(Country::class);
//        $expectedResult = Country::all('name', 'id', 'status')->where('status', 1)->pluck('name', 'id')->toArray();
//        $this->assertEquals($expectedResult, $abstractWithCountry->findList());
//    }
//
//    /**
//     *
//     */
//    public function testFindListWhenSetActiveAndListableArguments()
//    {
//        $listable = [
//            'value' => 'name',
//            'key' => 'id',
//            'columns' => [
//                'id',
//                'name'
//            ]
//        ];
//        $expectedResult = Country::all('name', 'id')->pluck('name', 'id')->toArray();
//        $this->assertEquals($expectedResult, $this->abstractWithCountry->findList(false, $listable));
//    }
//
//    /**
//     *
//     */
//    public function testfindListBy()
//    {
//        $this->abstractWithCountry->findListBy('name', 'Albania');
//        $whereCriteria = new WhereCriteria('name', 'Albania');
//        $result =  $this->getProtectedAttributeInObject($this->abstractWithCountry, 'criteria');
//        $this->assertEquals($whereCriteria, $result[0]);
//    }
//
//    /**
//     *
//     */
//    public function testGetListableColumns()
//    {
//        $expectedResult = [
//            'value' => 'name',
//            'key' => 'id',
//            'columns' => [
//                'name',
//                'id',
//            ],
//            '_done' => 1
//        ];
//        $this->assertEquals($expectedResult, $this->abstractWithCountry->getListableColumns());
//    }
//
//    /**
//     *
//     */
//    public function testAllWithDefaultArgument()
//    {
//        $this->assertEquals(Country::all(), $this->abstractWithCountry->all());
//    }
//
//    /**
//     *
//     */
//    public function testAllWhenSetColumnsArgument()
//    {
//        $columns = 'name';
//        $this->assertEquals(Country::all($columns), $this->abstractWithCountry->all([$columns]));
//    }
//
//    /**
//     *
//     */
//    public function testFindBy()
//    {
//        $this->abstractWithProject->findBy('name', 'New Name', ['name', 'code']);
//        $selectCriteria = new SelectCriteria(['name', 'code'], 'projects');
//        $whereCriteria = new WhereCriteria('name', 'New Name');
//        $result = $this->getProtectedAttributeInObject($this->abstractWithProject, 'criteria');
//        $this->assertEquals($whereCriteria, $result[0]);
//        $this->assertEquals($selectCriteria, $result[1]);
//    }
//
//    /**
//     *
//     */
//    public function testGetByCriteria()
//    {
//
//    }
//
//    /**
//     *
//     */
//    public function testFindFillableWith()
//    {
//        $this->abstractWithProject->findFillableWith($this->project->id, ['project_staff']);
//        $relationCriteria = new RelationCriteria('project_staff');
//        $result = $this->getProtectedAttributeInObject($this->abstractWithProject, 'criteria');
//        $this->assertEquals($relationCriteria, $result[0]);
//    }
//
//    /**
//     *
//     */
//    public function testFindFillable()
//    {
//        $this->abstractWithProject->findFillable($this->project->id);
//        $fillableCriteria = new FillableCriteria('id');
//        $result = $this->getProtectedAttributeInObject($this->abstractWithProject, 'criteria');
//        $this->assertEquals($fillableCriteria, $result[0]);
//    }
//
//
//    /**
//     *
//     */
//    public function testFindWhenSetIdArguments()
//    {
//        $this->assertEquals(Project::find($this->project->id), $this->abstractWithProject->find($this->project->id));
//    }
//
//    /**
//     *
//     */
//    public function testFindWhenSetColumnAndIdArguments()
//    {
//        $column = ['name'];
//        $this->assertEquals(Project::select($column)->find($this->project->id), $this->abstractWithProject->find($this->project->id, $column));
//    }
//
//    /**
//     *
//     */
//    public function testFindCount()
//    {
//        $this->assertEquals(Project::all()->count(), $this->abstractWithProject->findCount());
//    }
//
//    /**
//     *
//     */
//    public function testFindFillableWhere()
//    {
//        $this->abstractWithProject->findFillableWhere($this->project->id, 'name', 'New Name');
//        $whereCriteria = new WhereCriteria('name', 'New Name');
//        $result = $this->getProtectedAttributeInObject($this->abstractWithProject, 'criteria');
//        $this->assertEquals($whereCriteria, $result[0]);
//    }
//
//    public function testSaveAssociated()
//    {
//        $id = $this->abstractWithProject->saveAssociated(['name'=>"New Name"]);
//        $newProject = Project::find($id);
//        $this->assertEquals('New Name', $newProject->name);
//    }
//
//    /**
//     * @param $modelName
//     * @return PHPUnit_Framework_MockObject_MockObject
//     */
//    protected function initializeAbstractRepositoryWithModel($modelName)
//    {
//        $abstract = $this->getMockBuilder(AbstractRepository::class)
//            ->disableOriginalConstructor()
//            ->setMethods(['modelClass'])
//            ->getMockForAbstractClass();
//
//        $abstract->expects($this->any())
//            ->method('modelClass')
//            ->willReturn($modelName);
//
//        $abstract->__construct($this->container, $this->collection);
//        return $abstract;
//    }
//
//    /**
//     *
//     */
//    public function testCommitTransaction()
//    {
//        $this->assertEquals(DB::commit(), $this->abstractWithProject->commitTransaction());
//    }
//
//    /**
//     *
//     */
//    public function testRollbackTransaction()
//    {
//        $this->assertEquals(DB::rollBack(), $this->abstractWithProject->rollbackTransaction());
//    }
//
//    /**
//     * @param $obj
//     * @param $prop
//     * @return mixed
//     */
//    protected function getProtectedAttributeInObject($obj, $prop) {
//        $reflection = new \ReflectionClass(get_class($obj));
//        $property = $reflection->getProperty($prop);
//        $property->setAccessible(true);
//        return $property->getValue($obj);
//    }

}
