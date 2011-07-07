<?php
require_once 'BaseTest.php';
class AutoConditionalCriterionTest extends BaseTest{

	/**
	 *
	 * @dataProvider getArgs
	 * @test
	 */
	public function withSimpleQuote($i, $column, $value, $comparision, $mutatorColumn = null, $mutatorValue = null)
	{
		$criterion = new AutoConditionalCriterion($column, $value, $comparision, $mutatorColumn, $mutatorValue);
		$criterion->setQuoteStrategy(new SimpleQuoteStrategy());

		$this->assertEquals($this->getExpected($i), $criterion->createSql());
	}

	/**
	 *
	 * @dataProvider getArgs
	 * @test
	 */
	public function withZendDbQuote($i, $column, $value, $comparision, $mutatorColumn = null, $mutatorValue = null)
	{
		$criterion = new AutoConditionalCriterion($column, $value, $comparision, $mutatorColumn, $mutatorValue);
		$criterion->setQuoteStrategy($this->getZendDbQuoteStrategy());

		$this->assertEquals($this->getExpected($i), $criterion->createSql());
	}

	/**
	 *
	 * @test
	 * @expectedException Exception
	 */
	public function badArgument()
	{
		$criterion = new AutoConditionalCriterion('language', new stdClass(), Criterion::AUTO);
		$criterion->setQuoteStrategy($this->getZendDbQuoteStrategy());

		$criterion->createSql();
	}

	/**
	 *
	 * @return array
	 */
	public function getArgs(){
		return array(
			//   i,  column, value, comparision, mutatorColumn, mutatorValue
			//equal
			array(0, 'pi', '3.1416', Criterion::AUTO),
			array(1, 'pi', 3.14159265358, Criterion::AUTO),
			array(2, 'id', '94159265358', Criterion::AUTO),
			array(4, 'system', 'mac os', Criterion::AUTO),
			array(5, 'system', array('mac os', 'linux'), Criterion::AUTO),
			array(6, 'is_admin', null, Criterion::AUTO),
		);
	}

	/**
	 *
	 * @param int $i
	 * @return string
	 */
	public function getExpected($i)
	{
		$expected = array(
		    //equal
		    0 => "`pi` = '3.1416'",
		    1 => "`pi` = 3.14159265358",
		    2 => "`id` = '94159265358'",
		    4 => "`system` LIKE 'mac os'",
		    5 => "`system` IN ('mac os', 'linux')",
		    6 => "`is_admin` IS NULL",
		);
		return $expected[$i];
	}

}