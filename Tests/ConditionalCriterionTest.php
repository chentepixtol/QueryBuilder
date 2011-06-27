<?php
require_once 'BaseTest.php';
class ConditionalCriterionTest extends BaseTest{

	/**
	 *
	 * @dataProvider getArgs
	 * @test
	 */
	public function main($i, $column, $value, $comparision, $mutatorColumn = null, $mutatorValue = null)
	{
		$criterion = new ConditionalCriterion($column, $value, $comparision, $mutatorColumn, $mutatorValue);
		$criterion->setQuoteStrategy($this->getZendDbQuoteStrategy());

		$this->assertEquals($this->getExpected($i), $criterion->createSql());
	}

	/**
	 *
	 * @return array
	 */
	public function getArgs(){
		return array(
			//   i,  column, value, comparision, mutatorColumn, mutatorValue
			//equal
			array(0, 'pi', '3.1416', Criterion::NOT_EQUAL),
			array(1, 'pi', '3.1416', Criterion::EQUAL),
			// likes
			array(2, 'pi', '3.1416', Criterion::JUST_LIKE),
			array(3, 'pi', '3.1416', Criterion::LIKE),
			array(4, 'pi', '3.1416', Criterion::LEFT_LIKE),
			array(5, 'pi', '3.1416', Criterion::RIGHT_LIKE),
			array(6, 'pi', '3.1416', Criterion::NOT_LIKE),
			array(7, 'pi', '3.1416', Criterion::NOT_JUST_LIKE),
			// likes with spaces
			array(8,  'system', 'mac os', Criterion::JUST_LIKE),
			array(9,  'system', 'mac os', Criterion::LIKE),
			array(10, 'system', 'mac os', Criterion::LEFT_LIKE),
			array(11, 'system', 'mac os', Criterion::RIGHT_LIKE),
			array(12, 'system', 'mac os', Criterion::NOT_LIKE),
			array(13, 'system', 'mac os', Criterion::NOT_JUST_LIKE),
			// IN
			array(14, 'systems', array('mac os', 'linux'), Criterion::IN),
			array(15, 'systems', array('mac os', 'linux'), Criterion::NOT_IN),
			// NULL
			array(16, 'in_house', null, Criterion::IS_NULL),
			array(17, 'in_house', null, Criterion::IS_NOT_NULL),
			// GREATHER O LOWER
			array(18, 'date', '2011-06-27', Criterion::GREATER_THAN),
			array(19, 'date', '2011-06-27', Criterion::GREATER_OR_EQUAL),
			array(20, 'date', '2011-06-27', Criterion::LESS_THAN),
			array(21, 'date', '2011-06-27', Criterion::LESS_OR_EQUAL),
			//mutators
			array(22, 'password', '123', Criterion::EQUAL, null, Criterion::PASSWORD),
			array(23, 'name', 'vicente', Criterion::EQUAL, Criterion::LOWER),
			array(24, 'name', 'VICENTE', Criterion::EQUAL, Criterion::UPPER),
			array(25, 'timestamp', '2011-06-27', Criterion::EQUAL, Criterion::DATE),
			array(26, 'timestamp', '06', Criterion::EQUAL, Criterion::MONTH),
			array(27, 'timestamp', '2011', Criterion::EQUAL, Criterion::YEAR),
			array(28, 'text', 'hello world', Criterion::EQUAL, Criterion::TRIM),
			//double quote
			array(29, '`pi`', '3.1416', Criterion::EQUAL),
			//with table or alias
			array(30, 'math.pi', '3.1416', Criterion::EQUAL),
			array(31, array('math', 'pi'), '3.1416', Criterion::EQUAL),
			//BETWEEN
			array(32, 'date', array('2011-01-01', '20011-01-31'), Criterion::BETWEEN),
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
		    0 => "`pi` != '3.1416'",
			1 => "`pi` = '3.1416'",
			//likes
			2 => "`pi` LIKE '3.1416'",
			3 => "`pi` LIKE '%3.1416%'",
			4 => "`pi` LIKE '%3.1416'",
			5 => "`pi` LIKE '3.1416%'",
			6 => "`pi` NOT LIKE '%3.1416%'",
			7 => "`pi` NOT LIKE '3.1416'",
			//likes with spaces
			8 => "`system` LIKE 'mac os'",
			9 => "`system` LIKE '%mac os%'",
			10 => "`system` LIKE '%mac os'",
			11 => "`system` LIKE 'mac os%'",
			12 => "`system` NOT LIKE '%mac os%'",
			13 => "`system` NOT LIKE 'mac os'",
			//in
			14 => "`systems` IN ('mac os', 'linux')",
			15 => "`systems` NOT IN ('mac os', 'linux')",
			//null
			16 => "`in_house` IS NULL",
			17 => "`in_house` IS NOT NULL",
			//null
			18 => "`date` > '2011-06-27'",
			19 => "`date` >= '2011-06-27'",
			20 => "`date` < '2011-06-27'",
			21 => "`date` <= '2011-06-27'",
			//mutators
			22 => "`password` = PASSWORD('123')",
			23 => "LOWER(`name`) = 'vicente'",
			24 => "UPPER(`name`) = 'VICENTE'",
			25 => "DATE(`timestamp`) = '2011-06-27'",
			26 => "MONTH(`timestamp`) = '06'",
			27 => "YEAR(`timestamp`) = '2011'",
			28 => "TRIM(`text`) = 'hello world'",
			//double quote
			29 => "`pi` = '3.1416'",
			//with table or alias
			30 => "`math`.`pi` = '3.1416'",
			31 => "`math`.`pi` = '3.1416'",
			// BETWEEN
			32 => "`date` BETWEEN '2011-01-01' AND '20011-01-31'",
		);
		return $expected[$i];
	}

}