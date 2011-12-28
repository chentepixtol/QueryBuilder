<?php

use Query\Expression;
use Query\Criteria;
use Query\Criterion;
use Query\Query;
use Query\SimpleQuoteStrategy;


require_once 'BaseTest.php';

class QueryTest extends BaseTest
{

	/**
	 *
	 * @test
	 * @dataProvider getStrategyQuote
	 */
	public function quote($strategyQuote)
	{
		$query = new Query($strategyQuote);
		$this->assertEquals($strategyQuote, $query->getQuoteStrategy());
	}

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
		$this->assertEquals(array('*'), $query->getDefaultColumn());

		$query->addColumn('name');
		$this->assertFalse($query->hasColumn('email'));
		$this->assertTrue($query->hasColumn('name'));
		$this->assertEquals('SELECT `name`', $query->createSelectSql());

		$query->removeColumn('name');
		$query->distinct()->addColumn('name');
		$this->assertFalse($query->hasColumn('email'));
		$this->assertTrue($query->hasColumn('name'));
		$this->assertEquals('SELECT DISTINCT `name`', $query->createSelectSql());

		$query->distinct(false)->removeColumn('name');
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

		$query->addColumn(new Expression("IF(user.role == 'admin' AND user.username LIKE '%root%')"), 'isSuperAdmin');
		$this->assertEquals("SELECT IF(user.role == 'admin' AND user.username LIKE '%root%') as `isSuperAdmin`", $query->createSelectSql());
		$query->removeColumn();

		$query->addColumn("IF(user.role == 'admin' AND user.username LIKE '%root%')", 'isSuperAdmin', Criterion::AS_EXPRESSION);
		$this->assertEquals("SELECT IF(user.role == 'admin' AND user.username LIKE '%root%') as `isSuperAdmin`", $query->createSelectSql());
		$query->removeColumn();

		$query->addColumn("created_at", 'month', Criterion::MONTH);
		$this->assertEquals("SELECT MONTH(`created_at`) as `month`", $query->createSelectSql());
		$query->removeColumn();

		$query->addColumns(array('column1', 'column2'));
		$this->assertEquals("SELECT `column1`, `column2`", $query->createSelectSql());
		$query->removeColumn();

		$query->addColumns(array('alias1'=>'column1', 'alias2'=>'column2'));
		$this->assertEquals("SELECT `column1` as `alias1`, `column2` as `alias2`", $query->createSelectSql());
		$query->removeColumn();

		$query->select('name');
		$this->assertEquals("SELECT `name`", $query->createSelectSql());

		$query->select('email', 'age');
		$this->assertEquals("SELECT `name`, `email`, `age`", $query->createSelectSql());
		$query->removeColumn();

		$query->setDefaultColumn(array('*'));;
		$this->assertEquals("SELECT *", $query->createSelectSql());

		$query->setDefaultColumn(array('User.*', 'Person.*'));
		$this->assertEquals("SELECT `User`.*, `Person`.*", $query->createSelectSql());
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
	 * @expectedException Query\Exception
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
	 * @expectedException Query\Exception
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
	public function joinUsingPart($strategyQuote)
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
	public function aliasInJoins($strategyQuote)
	{
		$query = new Query($strategyQuote);

		$query->innerJoinUsing('system_prefixed_table_users', 'id_item', 'User');
		$this->assertEquals("INNER JOIN `system_prefixed_table_users` as `User` USING( `id_item` )", $query->createJoinSql());

		$query = new Query($strategyQuote);

		$query->innerJoinOn('system_prefixed_table_users', 'User')->add('User.id_person', 'Person.id_person', Criterion::EQUAL, null, Criterion::AS_FIELD);
		$this->assertEquals("INNER JOIN `system_prefixed_table_users` as `User` ON( `User`.`id_person` = `Person`.`id_person` )", $query->createJoinSql());

		$query->removeJoin('User');
		$this->assertEquals('', $query->createJoinSql());

	}

	/**
	 *
	 * @test
	 * @dataProvider getStrategyQuote
	 */
	public function joinOnPart($strategyQuote)
	{
		$query = new Query($strategyQuote);

		$this->assertEquals('', $query->createJoinSql());

		$query->innerJoinOn('User')->add('Person.id_person', 'User.id_person', Criterion::EQUAL, null, Criterion::AS_FIELD);
		$this->assertTrue($query->createJoinSql() == $query->createJoinSql());
		$this->assertEquals('INNER JOIN `User` ON( `Person`.`id_person` = `User`.`id_person` )', $query->createJoinSql());
		$query->removeJoin('User');

		$query->leftJoinOn('User')->add('Person.id_person', 'User.id_person', Criterion::EQUAL, null, Criterion::AS_FIELD);
		$this->assertTrue($query->createJoinSql() == $query->createJoinSql());
		$this->assertEquals('LEFT JOIN `User` ON( `Person`.`id_person` = `User`.`id_person` )', $query->createJoinSql());
		$query->removeJoin('User');

		$query->rightJoinOn('User')->add('Person.id_person', 'User.id_person', Criterion::EQUAL, null, Criterion::AS_FIELD);
		$this->assertTrue($query->createJoinSql() == $query->createJoinSql());
		$this->assertEquals('RIGHT JOIN `User` ON( `Person`.`id_person` = `User`.`id_person` )', $query->createJoinSql());
		$query->removeJoin('User');
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
	public function hasJoin($strategyQuote)
	{
		$query = new Query($strategyQuote);

		$query->innerJoinOn('system_prefixed_table_users')->add('User.id_person', 'Person.id_person', Criterion::EQUAL, null, Criterion::AS_FIELD);

		$this->assertTrue($query->hasJoin('system_prefixed_table_users'));
		$this->assertFalse($query->hasJoin('my_table'));

		$query = new Query($strategyQuote);

		$query->innerJoinOn('system_prefixed_table_users', 'User')->add('User.id_person', 'Person.id_person', Criterion::EQUAL, null, Criterion::AS_FIELD);

		$this->assertTrue($query->hasJoin('User'));
		$this->assertFalse($query->hasJoin('system_prefixed_table_users'));
	}

	/**
	 *
	 * @test
	 * @dataProvider getStrategyQuote
	 */
	public function manyJoins($strategyQuote)
	{
		$query = new Query($strategyQuote);
		$query->innerJoinOn('users')->add('id_user', 'id_person')->endJoin()
			->innerJoinOn('emails')->add('id_person', 'id_person')->endJoin();
		$this->assertEquals("INNER JOIN `users` ON( `id_user` LIKE 'id_person' ) INNER JOIN `emails` ON( `id_person` LIKE 'id_person' )", $query->createJoinSql());
	}

	/**
	 *
	 * @test
	 * @dataProvider getStrategyQuote
	 */
	public function group($strategyQuote)
	{
		$query = new Query($strategyQuote);
		$query->addGroupBy('month');

		$this->assertEquals("GROUP BY `month`", $query->createGroupSql());

		$query = new Query($strategyQuote);
		$query->addGroupBy('sales.month');

		$this->assertEquals("GROUP BY `sales`.`month`", $query->createGroupSql());

		$query->addGroupBy('sales.year');
		$this->assertTrue($query->createGroupSql() === $query->createGroupSql());
		$this->assertEquals("GROUP BY `sales`.`month`, `sales`.`year`", $query->createGroupSql());
		$this->assertEquals(array('sales.month', 'sales.year'), $query->getGroupByColumns());

		$query = new Query($strategyQuote);
		$query->addGroupBy(array('sales.month', 'sales.year'));
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
		$query->orderBy('Person.birthdate', Query::ASC);

		$this->assertEquals('ORDER BY `Person`.`birthdate` ASC', $query->createOrderSql());

		$query = new Query($strategyQuote);
		$query->addAscendingOrderBy('Person.birthdate');

		$this->assertEquals('ORDER BY `Person`.`birthdate` ASC', $query->createOrderSql());

		$query->addDescendingOrderBy('Person.name');
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
	 * @test
	 * @dataProvider getStrategyQuote
	 */
	public function wherePart($strategyQuote)
	{
		$query = new Query($strategyQuote);
		$query2 = new Query($strategyQuote);

		$this->assertTrue($query->where() instanceof Criteria);
		$this->assertTrue($query->where()->isEmpty());
		$this->assertEquals('', $query->createWhereSql());

		$query->where()->add('mycol', 'myvalue', Criterion::EQUAL);
		$this->assertTrue($query->contains('mycol'));
		$this->assertEquals("WHERE ( `mycol` = 'myvalue' )", $query->createWhereSql());

		$query2->whereAdd('mycol', 'myvalue', Criterion::EQUAL);
		$this->assertEquals("WHERE ( `mycol` = 'myvalue' )", $query->createWhereSql());

		$this->assertEquals($query->createWhereSql(), $query2->createWhereSql());
	}

	/**
	 *
	 * @test
	 * @dataProvider getStrategyQuote
	 */
	public function criteria($strategyQuote)
	{
		$query = new Query($strategyQuote);
		$query->from('users')
			->where()->setOR()
				->add('stage1', 1)
				->add('stage1', 1)
					->setAND()
						->add('stage2', 2)
						->add('stage2', 2);
		$this->assertEquals("SELECT * FROM `users` WHERE ( `stage1` = 1 OR `stage1` = 1 OR ( `stage2` = 2 AND `stage2` = 2 ) )", $query->createSql());

		$query->where()->add('stage3', 3);
		$this->assertEquals("SELECT * FROM `users` WHERE ( `stage1` = 1 OR `stage1` = 1 OR ( `stage2` = 2 AND `stage2` = 2 AND `stage3` = 3 ) )", $query->createSql());

		$query->where()->root()->add('stage1', 1);
		$this->assertEquals("SELECT * FROM `users` WHERE ( `stage1` = 1 OR `stage1` = 1 OR ( `stage2` = 2 AND `stage2` = 2 AND `stage3` = 3 ) OR `stage1` = 1 )", $query->createSql());
	}

	/**
	 *
	 * @test
	 * @dataProvider getStrategyQuote
	 */
	public function havingPart($strategyQuote)
	{
		$query = new Query($strategyQuote);

		$this->assertTrue($query->having() instanceof Criteria);
		$this->assertTrue($query->having()->isEmpty());
		$this->assertEquals('', $query->createHavingSql());

		$query->having()->add('mycol', 'myvalue', Criterion::EQUAL);
		$this->assertEquals('HAVING ( `mycol` = \'myvalue\' )', $query->createHavingSql());
	}

	/**
	 *
	 * @test
	 * @dataProvider getStrategyQuote
	 */
	public function all($strategyQuote)
	{
		$query = new Query($strategyQuote);

		$query->addColumn('User.*')
			->from('users', 'User')
			->innerJoinOn('Person')
				->add('User.id_person', 'Person.id_person', Criterion::EQUAL, null, Criterion::AS_FIELD)
			->endJoin()
			->where()
				->add('User.status', 'active')
				->add('Person.name', 'Vicente', Criteria::LEFT_LIKE)
			->endWhere()
			->addGroupBy('User.group')
			->addAscendingOrderBy('Person.age')
			->setLimit(10)
		;

		$this->assertTrue($query->createBeautySql() == $query->createBeautySql());
		$this->assertEquals("SELECT `User`.* FROM `users` as `User` INNER JOIN `Person` ON( `User`.`id_person` = `Person`.`id_person` ) WHERE ( `User`.`status` LIKE 'active' AND `Person`.`name` LIKE '%Vicente' ) GROUP BY `User`.`group` ORDER BY `Person`.`age` ASC LIMIT 10", $query->createSql());
	}

	/**
	 *
	 * @test
	 * @dataProvider getStrategyQuote
	 */
	public function intoOutfile($strategyQuote)
	{
		$query = new Query($strategyQuote);
		$query->from('users')->intoOutfile('users.csv');
		$this->assertEquals("SELECT * FROM `users` INTO OUTFILE 'users.csv' FIELDS TERMINATED BY ',' ENCLOSED BY '\"' ESCAPED BY '\\\\' LINES TERMINATED BY '\\r\\n'", $query->createSql());
	}

	/**
	 *
	 * @test
	 * @dataProvider getStrategyQuote
	 */
	public function cloning($strategyQuote)
	{
		$query = new Query($strategyQuote);
		$query->select('username', 'name', 'email')
			->from('users', 'user')
			->innerJoinUsing('persons', 'id_person')
			->where()
				->add('username', 'chentepixtol')
				->setOR()
					->add('email', 'vicentemmor%')
					->add('email', 'chentepixtol%')
				->end()
			->endWhere()
			->addAscendingOrderBy('user.id_user');
		$sql = "SELECT `username`, `name`, `email` FROM `users` as `user` INNER JOIN `persons` USING( `id_person` ) WHERE ( `username` LIKE 'chentepixtol' AND ( `email` LIKE 'vicentemmor%' OR `email` LIKE 'chentepixtol%' ) ) ORDER BY `user`.`id_user` ASC";
		$this->assertEquals($sql, $query->createSql());

		$query2 = clone $query;
		$query2->distinct()->select('age')->from('administrators')
			->innerJoinUsing('emails', 'id_person')
				->where()->add('age', 18, Criterion::GREATER_OR_EQUAL)->endWhere()
			->addAscendingOrderBy('age')
			->addGroupBy('id_person')
			->having()->add('age', 18, Criterion::GREATER_OR_EQUAL)->endHaving()
			->setLimit(100)->setOffset(20);

		$this->assertEquals($sql, $query->createSql());
	}

	/**
	 *
	 * @test
	 * @dataProvider getStrategyQuote
	 */
	public function cloning2($strategyQuote)
	{
		$query = new Query($strategyQuote);
		$query->select('username')
			->from('users', 'user')
			->where()
				->add('username', 'chentepixtol')
				->setOR()
					->add('email', 'vicentemmor%')
					->add('email', 'chentepixtol%')
				->end()
			->endWhere();
		$sql = "SELECT `username` FROM `users` as `user` WHERE ( `username` LIKE 'chentepixtol' AND ( `email` LIKE 'vicentemmor%' OR `email` LIKE 'chentepixtol%' ) )";
		$this->assertEquals($sql, $query->createSql());

		$query2 = clone $query;
		$query2->where()->add('id_company', 1);

		$this->assertEquals($sql, $query->createSql());
		$sql = "SELECT `username` FROM `users` as `user` WHERE ( `username` LIKE 'chentepixtol' AND ( `email` LIKE 'vicentemmor%' OR `email` LIKE 'chentepixtol%' ) AND `id_company` = 1 )";
		$this->assertEquals($sql, $query2->createSql());
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