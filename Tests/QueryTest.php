<?php
require_once 'BaseTest.php';
class QueryTest extends BaseTest
{

	/**
	 *
	 * @test
	 * @dataProvider getStrategyQuote
	 */
	public function selectPart($strategyQuote)
	{
		$query = new Query($strategyQuote);
		$this->assertTrue($query->createSelectSql() === $query->createSelectSql());
		$this->assertEquals('SELECT *', $query->createSelectSql());

		$query->addColumn('name');
		$this->assertEquals('SELECT `name`', $query->createSelectSql());

		$query->removeColumn('name');
		$this->assertEquals('SELECT *', $query->createSelectSql());

		$query->addColumn('User.name');
		$this->assertEquals('SELECT `User`.`name`', $query->createSelectSql());
		$query->removeColumn('User.name');
		$this->assertEquals('SELECT *', $query->createSelectSql());

		$query->addColumn(array('User','name'));
		$this->assertEquals('SELECT `User`.`name`', $query->createSelectSql());
		$query->removeColumn(array('User','name'));
		$this->assertEquals('SELECT *', $query->createSelectSql());

		$query->addColumn(array('User','name'), 'u_name');
		$this->assertEquals('SELECT `User`.`name` as `u_name`', $query->createSelectSql());

		$query->addColumn(array('User','password'), 'u_password');
		$this->assertEquals('SELECT `User`.`name` as `u_name`, `User`.`password` as `u_password`', $query->createSelectSql());

		$query->removeColumn();
		$this->assertEquals('SELECT *', $query->createSelectSql());

		$query->addColumn(array('User','*'));
		$this->assertEquals('SELECT `User`.*', $query->createSelectSql());
		$query->removeColumn();

		$query->addColumn('User.*');
		$this->assertEquals('SELECT `User`.*', $query->createSelectSql());
		$query->removeColumn();
	}

	/**
	 *
	 * @return array
	 */
	public function getStrategyQuote(){
		return array(
			array($this->getZendDbQuoteStrategy()),
			array(new SimpleQuoteStrategy()),
		);
	}


}