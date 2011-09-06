<?php
use Query\Criterion;
use Query\Expression;
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
		$criteria = $this->createCriteria();

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
		$criteria = $this->createCriteria();

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
		$criteria = $this->createCriteria();

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
		$criteria = $this->createCriteria();

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
		$criteria1 = $this->createCriteria();
		$criteria1->setAND()
			->add('name', 'chente')
			->add('nick', 'chentepixtol');

		$criteria2 = $this->createCriteria();
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
		$criteria1 = $this->createCriteria();
		$criteria1->setAND()
			->add('name', 'chente')
			->add('nick', 'chentepixtol');

		$criteria2 = $this->createCriteria();
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
		$criteria = $this->createCriteria();
		$this->assertEquals(0, $criteria->count());

		$criteria->add('column1', 'value1');
		$this->assertEquals(1, $criteria->count());


		$criteria = $this->createCriteria();
		$this->assertEquals(0, $criteria->count());

		$criteria->multipleAdd(
		array(
			array('column1', 'value1'),
			array('column2', 'value2'),
		));
		$this->assertEquals("( `column1` LIKE 'value1' AND `column2` LIKE 'value2' )", $criteria->createSql());
		$this->assertEquals(2, $criteria->count());
	}

	/**
	 *
	 * @test
	 */
	public function alias()
	{
		$criteria = $this->createCriteria();
		$criteria->equal('username', 'chentepixtol');
		$this->assertEquals("( `username` = 'chentepixtol' )", $criteria->createSql());

		$criteria = $this->createCriteria();
		$criteria->notEqual('username', 'chentepixtol');
		$this->assertEquals("( `username` != 'chentepixtol' )", $criteria->createSql());

		$criteria = $this->createCriteria();
		$criteria->like('username', 'chentepixtol');
		$this->assertEquals("( `username` LIKE '%chentepixtol%' )", $criteria->createSql());

		$criteria = $this->createCriteria();
		$criteria->justlike('username', 'chentepixtol');
		$this->assertEquals("( `username` LIKE 'chentepixtol' )", $criteria->createSql());

		$criteria = $this->createCriteria();
		$criteria->in('username', array('chentepixtol', 'vicentemmor'));
		$this->assertEquals("( `username` IN ('chentepixtol', 'vicentemmor') )", $criteria->createSql());

		$criteria = $this->createCriteria();
		$criteria->notIn('username', array('chentepixtol', 'vicentemmor'));
		$this->assertEquals("( `username` NOT IN ('chentepixtol', 'vicentemmor') )", $criteria->createSql());

		$criteria = $this->createCriteria();
		$criteria->isNull('username');
		$this->assertEquals("( `username` IS NULL )", $criteria->createSql());

		$criteria = $this->createCriteria();
		$criteria->isNotNull('username');
		$this->assertEquals("( `username` IS NOT NULL )", $criteria->createSql());

		$criteria = $this->createCriteria();
		$criteria->range('points', '10,20,30-35');
		$this->assertEquals("( `points` IN (10, 20, 30, 31, 32, 33, 34, 35) )", $criteria->createSql());
	}

	/**
	 *
	 * @test
	 */
	public function equalFields()
	{
		$criteria = $this->createCriteria();
		$criteria->equalFields('User.id_person', 'Person.id_person');

		$this->assertEquals("( `User`.`id_person` = `Person`.`id_person` )", $criteria->createSql());
	}

	/**
	 *
	 * @test
	 */
	public function containsTest(){
		$criteria = $this->createCriteria();
		$criteria->add('stage1', 1)
			->setOR()
				->add('stage2', 2)
				->add('stage3', 3);
		$this->assertFalse($criteria->contains('username'));
		$this->assertTrue($criteria->contains('stage1'));
		$this->assertTrue($criteria->contains('stage2'));
		$this->assertTrue($criteria->contains('stage3'));
	}

	/**
	 *
	 * @test
	 */
	public function removeTest()
	{
		$criteria = $this->createCriteria();
		$criteria->add('stage1', 1)
			->setOR()
				->add('stage2', 2)
				->add('stage3', 3);

		$criteria->remove('stage2');

		$this->assertFalse($criteria->contains('username'));
		$this->assertTrue($criteria->contains('stage1'));
		$this->assertFalse($criteria->contains('stage2'));
		$this->assertTrue($criteria->contains('stage3'));
		$this->assertEquals("( `stage1` = 1 AND ( `stage3` = 3 ) )", $criteria->createSql());

		$criteria->remove('stage3');
		$this->assertFalse($criteria->contains('username'));
		$this->assertTrue($criteria->contains('stage1'));
		$this->assertFalse($criteria->contains('stage2'));
		$this->assertFalse($criteria->contains('stage3'));
		$this->assertEquals("( `stage1` = 1 )", $criteria->createSql());

		$criteria->remove('stage1');
		$this->assertFalse($criteria->contains('username'));
		$this->assertFalse($criteria->contains('stage1'));
		$this->assertFalse($criteria->contains('stage2'));
		$this->assertFalse($criteria->contains('stage3'));
		$this->assertEquals("( 1 )", $criteria->createSql());

		$criteria->add(new Expression('temp'), 1);
		$this->assertEquals("( temp = 1 )", $criteria->createSql());

		$criteria->remove(new Expression('temp'));
		$this->assertEquals("( 1 )", $criteria->createSql());
	}

	/**
	 *
	 * @test
	 */
	public function replace()
	{
		$criteria = $this->createCriteria();
		$criteria->add('username', 'chentepixtol');
		$criteria->replace('username', 'vicentemmor');
		$this->assertEquals("( `username` LIKE 'vicentemmor' )", $criteria->createSql());

		$criteria->replace('username', 'chente_pixtol', Criterion::EQUAL);
		$this->assertEquals("( `username` = 'chente_pixtol' )", $criteria->createSql());

		$criteria->setOR()->add('email', 'gmail')->add('domain', 'com');
		$this->assertEquals("( `username` = 'chente_pixtol' AND ( `email` LIKE 'gmail' OR `domain` LIKE 'com' ) )", $criteria->createSql());

		$criteria->replace('domain', 'com.mx');
		$this->assertEquals("( `username` = 'chente_pixtol' AND ( `email` LIKE 'gmail' OR `domain` LIKE 'com.mx' ) )", $criteria->createSql());
	}

	/**
	 *
	 * @return Criteria
	 */
	protected function createCriteria()
	{
		$criteria = new Criteria();
		$criteria->setQuoteStrategy(new SimpleQuoteStrategy());
		return $criteria;
	}


}