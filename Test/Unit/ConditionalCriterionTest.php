<?php

namespace Test\Unit;

use Query\Expression;
use Query\AutoConditionalCriterion;
use Query\SimpleQuoteStrategy;
use Query\ConditionalCriterion;
use Query\Criterion;
use Query\Criteria;

class ConditionalCriterionTest extends BaseTest
{

    /**
     *
     * @test
     */
    public function factory()
    {
        $criterion = ConditionalCriterion::factory('column', 'value', Criterion::EQUAL);
        $this->assertTrue($criterion instanceof ConditionalCriterion);
        $this->assertFalse($criterion instanceof AutoConditionalCriterion);

        $criterion = ConditionalCriterion::factory('column', 'value', Criterion::AUTO);
        $this->assertTrue($criterion instanceof ConditionalCriterion);
        $this->assertTrue($criterion instanceof AutoConditionalCriterion);
    }

    /**
     * @test
     */
    public function containsTest()
    {
        $criterion = ConditionalCriterion::factory('username', 'chentepixtol', Criterion::EQUAL);
        $this->assertFalse($criterion->contains('email'));
        $this->assertTrue($criterion->contains('username'));
    }

    /**
     *
     * @dataProvider getArgs
     * @test
     */
    public function withSimpleQuote($i, $column, $value, $comparision, $mutatorColumn = null, $mutatorValue = null)
    {
        $criterion = new ConditionalCriterion($column, $value, $comparision, $mutatorColumn, $mutatorValue);
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
            //IDS
            array(33, 'id', 5115, Criterion::EQUAL),
            array(34, 'User.id_person', 'Person.id_person', Criteria::EQUAL, null, Criterion::AS_FIELD),
            //RANGE
            array(35, 'User.role', '1-2,3,5-8', Criteria::RANGE),
            // IN
            array(36, 'numbers', array(1,2,3,4), Criterion::IN),
            array(37, 'numbers', array('1','2','3','4'), Criterion::IN),
            // Expression
            array(38, 'status', new Expression('CONCAT(role, status)'), Criterion::EQUAL),
            array(39, new Expression('IF(status, status, 5)'), 5, Criterion::NOT_EQUAL),
            array(40, 'MONTH(created_at)', 5, Criterion::EQUAL, Criterion::AS_EXPRESSION),
            array(41, 'created_at', 'MONTH(updated_at)', Criterion::EQUAL, null, Criterion::AS_EXPRESSION),
            array(42, 'MONTH(created_at)', 'MONTH(updated_at)', Criterion::EQUAL, Criterion::AS_EXPRESSION, Criterion::AS_EXPRESSION),
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
            33 => "`id` = 5115",
            34 => '`User`.`id_person` = `Person`.`id_person`',
            // RANGE
            35 => "`User`.`role` IN (1, 2, 3, 5, 6, 7, 8)",
            // IN
            36 => "`numbers` IN (1, 2, 3, 4)",
            37 => "`numbers` IN ('1', '2', '3', '4')",
            //Expression
            38 => "`status` = CONCAT(role, status)",
            39 => "IF(status, status, 5) != 5",
            40 => "MONTH(created_at) = 5",
            41 => "`created_at` = MONTH(updated_at)",
            42 => "MONTH(created_at) = MONTH(updated_at)",
        );
        return $expected[$i];
    }

}