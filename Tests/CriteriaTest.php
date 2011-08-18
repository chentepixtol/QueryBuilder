<?php
use Query\SimpleQuoteStrategy;
use Query\Criteria;


require_once 'BaseTest.php';

class CriteriaTest extends BaseTest
{

	/**
	 *
	 * @test
	 */
	public function basicAnd()
	{
		$criteria = new Criteria();
		$criteria->setQuoteStrategy(new SimpleQuoteStrategy());

		$this->assertTrue($criteria->isEmpty());
		$this->assertNull($criteria->getQuery());
		$this->assertTrue($criteria->endHaving() === $criteria->endJoin());
		$this->assertTrue($criteria->endWhere() === $criteria->getQuery());
		$this->assertEquals('( 1 )', $criteria->createSql());

		$criteria->add('col1', 1);
		$criteria->add('col2', 2);

		$this->assertEquals('( `col1` = 1 AND `col2` = 2 )', $criteria->createSql());
	}

	/**
	 *
	 * @test
	 */
	public function basicOr()
	{
		$criteria = new Criteria();
		$criteria->setQuoteStrategy(new SimpleQuoteStrategy());

		$this->assertTrue($criteria->isEmpty());
		$this->assertNull($criteria->getQuery());
		$this->assertTrue($criteria->endHaving() === $criteria->endJoin());
		$this->assertTrue($criteria->endWhere() === $criteria->getQuery());
		$this->assertEquals('( 1 )', $criteria->createSql());

		$criteria->setOR();
		$criteria->add('col1', 1);
		$criteria->add('col2', 2);

		$this->assertEquals('( `col1` = 1 OR `col2` = 2 )', $criteria->createSql());
	}

	/**
	 *
	 * @test
	 */
	public function complexAnd()
	{
		$criteria = new Criteria();
		$criteria->setQuoteStrategy(new SimpleQuoteStrategy());

		$this->assertTrue($criteria->isEmpty());
		$this->assertNull($criteria->getQuery());
		$this->assertTrue($criteria->endHaving() === $criteria->endJoin());
		$this->assertTrue($criteria->endWhere() === $criteria->getQuery());
		$this->assertEquals('( 1 )', $criteria->createSql());

		$criteria
			->add('col1', 1)
			->add('col2', 2)
			->setOR()
				->add('col3', 3)
				->add('col4', 4)
			->end();

		$this->assertEquals('( `col1` = 1 AND `col2` = 2 AND ( `col3` = 3 OR `col4` = 4 ) )', $criteria->createSql());
	}

	/**
	 *
	 * @test
	 */
	public function complexOr()
	{
		$criteria = new Criteria();
		$criteria->setQuoteStrategy(new SimpleQuoteStrategy());

		$this->assertTrue($criteria->isEmpty());
		$this->assertNull($criteria->getQuery());
		$this->assertTrue($criteria->endHaving() === $criteria->endJoin());
		$this->assertTrue($criteria->endWhere() === $criteria->getQuery());
		$this->assertEquals('( 1 )', $criteria->createSql());

		$criteria
			->setOR()
				->add('col1', 1)
				->add('col2', 2)
				->setAND()
					->add('col3', 3)
					->add('col4', 4)
				->end();

		$this->assertEquals('( `col1` = 1 OR `col2` = 2 OR ( `col3` = 3 AND `col4` = 4 ) )', $criteria->createSql());
	}

	/**
	 *
	 * @test
	 */
	public function merge()
	{
		$criteria1 = new Criteria();
		$criteria1->setQuoteStrategy(new SimpleQuoteStrategy());
		$criteria1->setAND()
			->add('name', 'chente')
			->add('nick', 'chentepixtol');

		$criteria2 = new Criteria();
		$criteria2->setQuoteStrategy(new SimpleQuoteStrategy());
		$criteria2->setOR()
			->add('mail', 'yahoo')
			->add('mail', 'hotmail');

		$criteria1->merge($criteria2);

		$this->assertEquals("( `name` LIKE 'chente' AND `nick` LIKE 'chentepixtol' AND ( `mail` LIKE 'yahoo' OR `mail` LIKE 'hotmail' ) )", $criteria1->createSql());
	}

	/**
	 *
	 * @test
	 */
	public function deepMerge()
	{
		$criteria1 = new Criteria();
		$criteria1->setQuoteStrategy(new SimpleQuoteStrategy());
		$criteria1->setAND()
			->add('name', 'chente')
			->add('nick', 'chentepixtol');

		$criteria2 = new Criteria();
		$criteria2->setQuoteStrategy(new SimpleQuoteStrategy());
		$criteria2->setOR()
			->add('mail', 'yahoo')
			->add('mail', 'hotmail')
			->setAND()
				->add('domain', 'mx')
				->add('country', 'mexico')
			->end();

		$criteria1->merge($criteria2);

		$this->assertEquals("( `name` LIKE 'chente' AND `nick` LIKE 'chentepixtol' AND ( `mail` LIKE 'yahoo' OR `mail` LIKE 'hotmail' OR ( `domain` LIKE 'mx' AND `country` LIKE 'mexico' ) ) )", $criteria1->createSql());
	}

	/**
	 *
	 * @test
	 */
	public function add()
	{
		$criteria = new Criteria();
		$criteria->setQuoteStrategy(new SimpleQuoteStrategy());
		$this->assertEquals(0, $criteria->count());

		$criteria->add('column1', 'value1');
		$this->assertEquals(1, $criteria->count());


		$criteria = new Criteria();
		$criteria->setQuoteStrategy(new SimpleQuoteStrategy());
		$this->assertEquals(0, $criteria->count());

		$criteria->multipleAdd(
		array(
			array('column1', 'value1'),
			array('column2', 'value2'),
		));
		$this->assertEquals("( `column1`  'value1' AND `column2`  'value2' )", $criteria->createSql());
		$this->assertEquals(2, $criteria->count());
	}

}