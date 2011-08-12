<?php
use Query\SimpleQuoteStrategy;
use Query\Criteria;


require_once 'BaseTest.php';
require_once 'Tests/Mock/MockQuery.php';
require_once 'Tests/Mock/MockCriteria.php';

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