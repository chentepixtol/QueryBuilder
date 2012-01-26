<?php

use Query\Expression;
use Query\Criteria;
use Query\Criterion;
use Query\Query;
use Query\SimpleQuoteStrategy;


require_once 'BaseTest.php';

class SQLInjectionTest extends BaseTest
{

    /**
     *
     * @test
     * @dataProvider getStrategyQuote
     */
    public function escapeQuote($strategyQuote)
    {
        $query = new Query($strategyQuote);
        $query->from('users')
            ->where()
                ->add('username', "admin'--", Criterion::EQUAL)
                ->add('password', 'password', Criterion::EQUAL)
            ->endWhere();
        $this->assertEquals("SELECT * FROM `users` WHERE ( `username` = 'admin\'--' AND `password` = 'password' )", $query->createSql());
    }

    /**
     *
     * @test
     * @dataProvider getStrategyQuote
     */
    public function inlineComments($strategyQuote)
    {
        $query = new Query($strategyQuote);
        $query->from('users')
            ->where()
                ->add('id', "10; DROP TABLE users /*", Criterion::EQUAL)
            ->endWhere();
        $this->assertEquals("SELECT * FROM `users` WHERE ( `id` = '10; DROP TABLE users /*' )", $query->createSql());
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