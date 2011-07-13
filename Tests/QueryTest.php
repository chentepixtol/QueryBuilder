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
	 * @test
	 * @dataProvider getStrategyQuote
	 */
	public function fromPart($strategyQuote)
	{
		$query = new Query($strategyQuote);

		$query->from('table_users');
		$this->assertEquals($query->createFromSql(), $query->createFromSql());
		$this->assertEquals("FROM `table_users`", $query->createFromSql());

		$query->removeFrom('table_users');

		$query->from('table_users', 'User');
		$this->assertEquals("FROM `table_users` as `User`", $query->createFromSql());

		$query->from('table_persons', 'Person');
		$this->assertEquals("FROM `table_users` as `User`, `table_persons` as `Person`", $query->createFromSql());
	}

	/**
	 *
	 * @test
	 * @expectedException Exception
	 */
	public function removeFromPart()
	{
		$query = new Query();
		$query->from('users')->from('persons');
		$this->assertEquals("FROM `users`, `persons`", $query->createFromSql());

		$query->removeFrom();
		$query->createFromSql();
	}

	/**
	 *
	 * @test
	 * @expectedException Exception
	 */
	public function emptyFrom(){
		$query = new Query();
		$query->createFromSql();
	}

	/**
	 *
	 * @test
	 * @dataProvider getStrategyQuote
	 */
	public function joinPart($strategyQuote)
	{
		$query = new Query($strategyQuote);

		$this->assertEquals('', $query->createJoinSql());

		$query->joinUsing('persons', 'id_person');
		$this->assertEquals($query->createJoinSql(), $query->createJoinSql());
		$this->assertEquals("JOIN `persons` USING( `id_person` )", $query->createJoinSql());
		$query->removeJoin('persons');

		$query->innerJoinUsing('users', 'id_user');
		$this->assertEquals("INNER JOIN `users` USING( `id_user` )", $query->createJoinSql());
		$query->removeJoin('users');

		$query->leftJoinUsing('users', 'id_user');
		$this->assertEquals("LEFT JOIN `users` USING( `id_user` )", $query->createJoinSql());
		$query->removeJoin('users');

		$query->rightJoinUsing('users', 'id_user');
		$this->assertEquals("RIGHT JOIN `users` USING( `id_user` )", $query->createJoinSql());
		$query->removeJoin('users');
	}

	/**
	 *
	 * @test
	 * @dataProvider getStrategyQuote
	 */
	public function removeAllJoins($strategyQuote)
	{
		$query = new Query($strategyQuote);

		$query->joinUsing('persons', 'id_person');
		$this->assertEquals("JOIN `persons` USING( `id_person` )", $query->createJoinSql());

		$query->innerJoinUsing('users', 'id_user');
		$this->assertEquals("JOIN `persons` USING( `id_person` ) INNER JOIN `users` USING( `id_user` )", $query->createJoinSql());

		$query->removeJoins();
		$this->assertEquals('', $query->createJoinSql());
	}

	/**
	 *
	 * @test
	 * @dataProvider getStrategyQuote
	 */
	public function group($strategyQuote)
	{
		$query = new Query($strategyQuote);
		$query->addGroupByColumn('month');

		$this->assertEquals("GROUP BY `month`", $query->createGroupSql());

		$query = new Query($strategyQuote);
		$query->addGroupByColumn('sales.month');

		$this->assertEquals("GROUP BY `sales`.`month`", $query->createGroupSql());

		$query->addGroupByColumn('sales.year');
		$this->assertTrue($query->createGroupSql() === $query->createGroupSql());
		$this->assertEquals("GROUP BY `sales`.`month`, `sales`.`year`", $query->createGroupSql());

		$this->assertEquals(array('sales.month', 'sales.year'), $query->getGroupByColumns());
	}

	/**
	 *
	 * @test
	 * @dataProvider getStrategyQuote
	 */
	public function order($strategyQuote)
	{
		$query = new Query($strategyQuote);
		$query->addAscendingOrderByColumn('Person.birthdate');

		$this->assertEquals('ORDER BY `Person`.`birthdate` ASC', $query->createOrderSql());

		$query->addDescendingOrderByColumn('Person.name');
		$this->assertTrue($query->createOrderSql() === $query->createOrderSql());
		$this->assertEquals('ORDER BY `Person`.`birthdate` ASC, `Person`.`name` DESC', $query->createOrderSql());
	}

	/**
	 *
	 * @test
	 * @dataProvider getStrategyQuote
	 */
	public function limit($strategyQuote)
	{
		$query = new Query($strategyQuote);
		$query->setLimit(10);
		$this->assertTrue($query->createLimitSql() === $query->createLimitSql());
		$this->assertEquals('LIMIT 10', $query->createLimitSql());
		$this->assertEquals(10, $query->getLimit());

		$query->setOffset(20);
		$this->assertEquals('LIMIT 10 OFFSET 20', $query->createLimitSql());
		$this->assertEquals(20, $query->getOffset());
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