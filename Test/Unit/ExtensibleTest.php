<?php

namespace Test\Unit;

use Test\Unit\Mock\MockCriteria;
use Test\Unit\Mock\MockQuery;
use Query\SimpleQuoteStrategy;
use Query\Criteria;

class ExtensibleTest extends BaseTest
{


    /**
     *
     * @test
     */
    public function main()
    {
        $this->assertTrue(MockQuery::create() instanceof MockQuery);

        $criteriaWhere = MockQuery::create()->where();
        $this->assertTrue($criteriaWhere instanceof MockCriteria);

        $criteriaHaving = MockQuery::create()->having();
        $this->assertTrue($criteriaHaving instanceof MockCriteria);

        $criteriaJoin = MockQuery::create()->joinOn('my_table');
        $this->assertTrue($criteriaJoin instanceof MockCriteria);

        $select  = MockQuery::create()->createSelectSql();
        $this->assertEquals("SELECT `MockTable`.*", $select);
    }

}