<?php

namespace Test\Unit;

use Query\NullQuoteStrategy;
use Query\SimpleQuoteStrategy;
use Query\QuoteStrategy;

class QuoteStrategyTest extends BaseTest
{

    /**
     *
     * @test
     * @dataProvider getArgs
     */
    public function simpleQuote($i, $value){
        $simple = new SimpleQuoteStrategy();
        $this->assertEquals($this->getExpected($i), $simple->quote($value));
    }

    /**
     *
     * @test
     */
    public function nullStrategyQuote()
    {
        $null = new NullQuoteStrategy();
        $this->assertEquals('1', $null->quote('1'));
        $this->assertEquals('1', $null->quoteColumn('1'));
        $this->assertEquals('1', $null->quoteTable('1'));
    }

    /**
     *
     * @test
     * @dataProvider getArgs
     */
    public function dbQuote($i, $value){
        $dbQuote = $this->getZendDbQuoteStrategy();
        $this->assertEquals($this->getExpected($i), $dbQuote->quote($value));
    }

    public function getArgs(){
        return array(
            array(1, 123),
            array(2, '123'),
            array(3, 'string'),
            array(4, array('linux','mac os')),
        );
    }


    public function getExpected($i){
        $array = array(
            1 => 123,
            2 => "'123'",
            3 => "'string'",
            4 => "'linux', 'mac os'",
        );
        return $array[$i];
    }

}