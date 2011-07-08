<?php
require_once 'BaseTest.php';
class QueryTest extends BaseTest
{

	/**
	 *
	 * @test
	 */
	public function selectPart()
	{
		$query = new Query();
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
	}


}