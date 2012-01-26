<?php
use Query\Range;

require_once 'BaseTest.php';

/**
 *
 * test suite para el kitbuilder
 * @author chente
 *
 */
class RangeTest extends BaseTest
{

    /**
     * (non-PHPdoc)
     * @see PHPUnit_Framework_TestCase::setUp()
     */
    public function setUp()
    {
    }

    /**
     * TESTS
     */

    /**
     *
     * @test
     * @dataProvider getInvalidStrings
     * @expectedException Query\Exception
     */
    public function invalidString($argument)
    {
        $range = new Range();
        $range->fromString($argument);
    }

    /**
     *
     * @test
     * @dataProvider getValidStrings
     */
    public function validString($argument)
    {
        $range = new Range();
        $range->fromString($argument);
        $this->assertEquals($range->toArray(), $this->getValidArrayFor($argument));
    }

    /**
     *
     * @test
     * @dataProvider getValidArrays
     * @param unknown_type $argument
     */
    public function validArray($argument){
        $range = new Range();
        $range->fromArray($argument);
        $this->assertEquals($this->getValidStringFor($argument), $range->toString());
    }

    /**
     *
     * @return array
     */
    public function getInvalidStrings(){
        return array(
            array('-'),
            array(','),
            array('4-,'),
            array('4,-'),
            array(',-'),
            array('-,'),
            array('5,1-2,'),
            array('1-2,'),
            array('1,'),
            array(',2'),
            array('-3'),
            array('-8,-6--5,2'),
        );
    }

    /**
     *
     * @return array
     */
    public function getValidStrings(){
        return array(
            array(' '),
            array(''),
            array(null),
            array(0),
            array('1'),
            array('1-2'),
            array('1-2,5'),
            array('5,1-2'),
            array('3,5-10,22'),
            array('1-2,5,6-8'),
        );
    }

    /**
     *
     * Enter description here ...
     * @param unknown_type $string
     * @return array
     */
    public function getValidArrayFor($string){
        $array = array(
            ' ' => array(),
            '' => array(),
            0 => array(),
            '1' => array(1),
            '1-2' => array(1,2),
            '1-2,5' => array(1,2,5),
            '5,1-2' => array(1,2,5),
            '3,5-10,22' => array(3,5,6,7,8,9,10,22),
            '1-2,5,6-8' => array(1,2,5,6,7,8),
        );
        return $array[$string];
    }

    /**
     *
     * @test
     */
    public function order()
    {
        $range = new Range();
        $range->fromArray(array(2,3,4,9,10,11,7));

        $range2 = new Range();
        $range2->fromArray(array(11,7,3,4,9,2,10));

        $this->assertEquals($range->toString(), $range2->toString());


        $range = new Range();
        $range->fromString('5,1-2');

        $range2 = new Range();
        $range2->fromString('1-2,5');

        $this->assertEquals($range->toArray(), $range2->toArray());
    }

    /**
     *
     * @test
     */
    public function unique(){
        $range = new Range();
        $range->fromArray(array(2,3));

        $range2 = new Range();
        $range2->fromArray(array(2,2,2,2,3,3,3,3));

        $this->assertEquals($range->toString(), $range2->toString());

        $range = new Range();
        $range->fromString('1,3');

        $range2 = new Range();
        $range2->fromString('1,1,1,3,3,3');

        $this->assertEquals($range->toArray(), $range2->toArray());
    }

    /**
     *
     * @test
     */
    public function equals()
    {
        $range = new Range();
        $range->fromArray(array(2,3,4,9,20,21,22));

        $range2 = new Range();
        $range2->fromString('2-4,9,20-22');

        $this->assertEquals($range->toString(), $range2->toString());
        $this->assertEquals($range->toArray(), $range2->toArray());
    }

    /**
     *
     * @test
     * @dataProvider getInvalidArrays
     * @expectedException Query\Exception
     */
    public function invalidArray($argument){
        $range = new Range();
        $range->fromArray($argument);
    }


    /**
     *
     * Enter description here ...
     * @param unknown_type $string
     * @return array
     */
    public function getValidArrays(){
        $array = array(
            array(array()),
            array(array(1)),
            array(array(5,6)),
            array(array(9,8)),
            array(array(2,3,4,9,10,11,7)),
            array(array('1','2','5','7','8')),
            array(array('100','102','150','101')),
        );
        return $array;
    }

    /**
     *
     * Enter description here ...
     * @param unknown_type $string
     * @return array
     */
    public function getInvalidArrays(){
        $stdClass = new stdClass();
        $stdClass->temp = 1;
        $array = array(
            array(array(-5,-8,-6,2)),
            array(array(array(4),array(2),array(1))),
            array(array('ret','ertre')),
            array(array('f34', '34534sd')),
            array(array($stdClass, $stdClass)),
            array(array('1.1', '1.2', '2.3')),
            array('notAnArray'),
        );
        return $array;
    }

    /**
     *
     * Enter description here ...
     * @param unknown_type $string
     * @return array
     */
    public function getValidStringFor($array){
        $strings = array(
            ''  => array(),
            '1' => array(1),
            '5-6' => array(5,6),
            '8-9' => array(9,8),
            '2-4,7,9-11' => array(2,3,4,9,10,11,7),
            '1-2,5,7-8' => array('1','2','5','7','8'),
            '100-102,150' => array('100','102','150','101'),
        );
        $string = array_search($array, $strings);
        return $string;
    }

}
