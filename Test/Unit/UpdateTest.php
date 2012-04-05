<?php

namespace Test\Unit;

use Query\Expression;

use Query\SimpleQuoteStrategy;
use Query\Criterion;
use Query\Update;

class UpdateTest extends BaseTest
{

    /**
     *
     * @test
     * @dataProvider getStrategyQuote
     */
    public function complete($strategyQuote){
        $update = Update::create($strategyQuote);
        $update->from('users')->addSet('status', 'active')->whereAdd('id_user', 1)->setLimit(1);

        $this->assertEquals("UPDATE `users` SET `status` = 'active' WHERE ( `id_user` = 1 ) LIMIT 1", $update->createSql());
    }

    /**
     *
     * @test
     * @dataProvider getStrategyQuote
     */
    public function constructor($strategyQuote){
        $update = Update::create($strategyQuote);
        $this->assertTrue($update instanceof Update);
        $this->assertTrue($update instanceof Criterion);
    }

    /**
     *
     * @test
     * @dataProvider getStrategyQuote
     * @expectedException Query\Exception
     */
    public function emptySet($strategyQuote){
        $update = Update::create($strategyQuote);
        $this->assertEquals("", $update->createSetSql());
    }

    /**
     *
     * @test
     * @dataProvider getStrategyQuote
     */
    public function addSet($strategyQuote){
        $update = Update::create($strategyQuote);
        $update->addSet('column1', 'myValue');
        $this->assertEquals("SET `column1` = 'myValue'", $update->createSetSql());

        $update->addSet('column2', 'otherValue');
        $this->assertEquals("SET `column1` = 'myValue', `column2` = 'otherValue'", $update->createSetSql());
    }

    /**
     *
     * @test
     * @dataProvider getStrategyQuote
     */
    public function addSets($strategyQuote){
        $update = Update::create($strategyQuote);
        $update->addSets(array(
            'column1' => 'myValue',
            'column2' => 'otherValue',
        ));
        $this->assertEquals("SET `column1` = 'myValue', `column2` = 'otherValue'", $update->createSetSql());
    }

    /**
     *
     * @test
     * @dataProvider getStrategyQuote
     */
    public function expressions($strategyQuote){
        $update = Update::create($strategyQuote);
        $update->addSet('updated_at', new Expression('NOW()'));
        $this->assertEquals("SET `updated_at` = NOW()", $update->createSetSql());

        $update->addSet('created_at', '2012-01-01 13:00:00', Criterion::DATE);
        $this->assertEquals("SET `updated_at` = NOW(), `created_at` = DATE('2012-01-01 13:00:00')", $update->createSetSql());

        $update = Update::create($strategyQuote);
        $update->addSet('created_at', "DATE('2012-01-01 13:00:00')", Criterion::AS_EXPRESSION);
        $this->assertEquals("SET `created_at` = DATE('2012-01-01 13:00:00')", $update->createSetSql());
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