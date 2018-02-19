<?php

namespace Tests\Criteria\Has;

use LaraRepo\Contracts\RepositoryInterface;
use LaraRepo\Criteria\Has\HasCriteria;
use LaraTest\Traits\AssertionTraits;
use LaraTest\Traits\MockTraits;

class HasCriteriaTest extends \TestCase
{
    use MockTraits, AssertionTraits;

    /**
     *
     */
    public function testApply()
    {
        $hasCriteria = new HasCriteria('col', 'value');
        $interface = $this->getMockForAbstract(RepositoryInterface::class);
        $modelQuery = $this->getMockObjectWithMockedMethods('has');
        $this->assertEquals($modelQuery, $hasCriteria->apply($modelQuery, $interface));
    }

    public function testApplyCheckCalledHas()
    {
        $hasCriteria = new HasCriteria('col', 'value');
        $interface = $this->getMockForAbstract(RepositoryInterface::class);
        $modelQuery = $this->getMockObjectWithMockedMethods('has');
        $this->expectCallMethodWithArgument($modelQuery, 'has', ['col', '=', 'value']);
        $hasCriteria->apply($modelQuery, $interface);
    }

}
