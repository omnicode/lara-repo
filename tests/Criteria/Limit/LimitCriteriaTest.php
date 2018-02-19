<?php

namespace Tests\Criteria\Limit;

use LaraRepo\Contracts\RepositoryInterface;
use LaraRepo\Criteria\Limit\LimitCriteria;
use LaraTest\Traits\AssertionTraits;
use LaraTest\Traits\MockTraits;

class LimitCriteriaTest extends \TestCase
{
    use MockTraits, AssertionTraits;

    /**
     *
     */
    public function testApply()
    {
        $lastCriteria = new LimitCriteria('limit');
        $interface = $this->getMockForAbstract(RepositoryInterface::class);
        $modelQuery = $this->getMockObjectWithMockedMethods('limit');
        $this->assertEquals($modelQuery, $lastCriteria->apply($modelQuery, $interface));
    }

    /**
     *
     */
    public function testApplyCheckCalledHas()
    {
        $lastCriteria = new LimitCriteria('limit');
        $interface = $this->getMockForAbstract(RepositoryInterface::class);
        $modelQuery = $this->getMockObjectWithMockedMethods('limit');
        $this->expectCallMethodWithArgument($modelQuery, 'limit', ['limit']);
        $lastCriteria->apply($modelQuery, $interface);
    }

}
