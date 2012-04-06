<?php

namespace Test\Unit;

use Query\SimpleQuoteStrategy;
use Query\Criterion;
use Query\Delete;

class DeleteTest extends BaseTest
{

    /**
     *
     * @test
     * @dataProvider getStrategyQuote
     */
    public function complete($strategyQuote){
        $delete = Delete::create($strategyQuote);
        $delete->from('users')->whereAdd('id_user', 1)->setLimit(1);

        $this->assertEquals("DELETE FROM `users` WHERE ( `id_user` = 1 ) LIMIT 1", $delete->createSql());
    }

    /**
     *
     * @test
     * @dataProvider getStrategyQuote
     */
    public function constructor($strategyQuote){
        $delete = Delete::create($strategyQuote);
        $this->assertTrue($delete instanceof Delete);
        $this->assertTrue($delete instanceof Criterion);
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