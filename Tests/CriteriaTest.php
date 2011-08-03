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

}