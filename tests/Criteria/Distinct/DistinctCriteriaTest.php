<?php

namespace Tests\Criteria\Distinct;

use LaraRepo\Contracts\RepositoryInterface;
use LaraRepo\Criteria\Distinct\DistinctCriteria;
use LaraTest\Traits\AssertionTraits;
use LaraTest\Traits\MockTraits;
use Tests\TestCase;

class DistinctCriteriaTest extends TestCase
{
    use MockTraits, AssertionTraits;

    /**
     *
     */
    public function testApply()
    {
        $distinctCriteria = new DistinctCriteria();
        $interface = $this->getMockForAbstract(RepositoryInterface::class);
        $modelQuery = $this->getMockObjectWithMockedMethods('distinct');
        $this->assertEquals($modelQuery, $distinctCriteria->apply($modelQuery, $interface));
    }

    public function testApplyCheckDistinctCalled()
    {
        $distinctCriteria = new DistinctCriteria();
        $interface = $this->getMockForAbstract(RepositoryInterface::class);
        $modelQuery = $this->getMockObjectWithMockedMethods('distinct');
        $this->expectCallMethod($modelQuery, 'distinct');
        $distinctCriteria->apply($modelQuery, $interface);
    }

}
