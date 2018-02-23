<?php

namespace Tests\Criteria\Group;

use LaraRepo\Contracts\RepositoryInterface;
use LaraRepo\Criteria\Group\GroupByCriteria;
use LaraTest\Traits\AssertionTraits;
use LaraTest\Traits\MockTraits;
use Tests\TestCase;

class GroupByCriteriaTest extends TestCase
{
    use MockTraits, AssertionTraits;

    /**
     *
     */
    public function testApply()
    {
        $groupByCriteria = new GroupByCriteria('col');
        $interface = $this->getMockForAbstract(RepositoryInterface::class, ['fixColumns']);
        $modelQuery = $this->getMockObjectWithMockedMethods('groupBy');
        $this->methodWillReturnTrue($interface, 'fixColumns');
        $this->assertEquals($modelQuery, $groupByCriteria->apply($modelQuery, $interface));
    }

    /**
     *
     */
    public function testApplyCheckCalledGroupBy()
    {
        $groupByCriteria = new GroupByCriteria('col');
        $interface = $this->getMockForAbstract(RepositoryInterface::class, ['fixColumns']);
        $modelQuery = $this->getMockObjectWithMockedMethods('groupBy');
        $this->methodWillReturnTrue($interface, 'fixColumns', ['col', null, null]);
        $this->expectCallMethodWithArgument($modelQuery, 'groupBy', [true]);
        $groupByCriteria->apply($modelQuery, $interface);
    }
}
