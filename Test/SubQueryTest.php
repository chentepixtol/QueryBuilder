<?php

use Query\Expression;
use Query\Criteria;
use Query\Criterion;
use Query\Query;
use Query\SimpleQuoteStrategy;


require_once 'BaseTest.php';

class SubQueryTest extends BaseTest
{

    /**
     *
     * @test
     * @dataProvider getProviders
     */
    public function subqueryIN($quoteStrategy)
    {
        $subquery = Query::create($quoteStrategy)->addColumn('User.id_role')->from('users', 'User');
        $query  = Query::create($quoteStrategy)->from('roles', 'Rol');
        $query->where()->add('Rol.id_role', $subquery, Criterion::IN);
        $this->assertEquals("SELECT * FROM `roles` as `Rol` WHERE ( `Rol`.`id_role` IN ( SELECT `User`.`id_role` FROM `users` as `User` ) )", $query->createSql());
    }

    /**
     *
     * @test
     * @dataProvider getProviders
     */
    public function subqueryColumn($quoteStrategy)
    {
        $subqueryColumn = Query::create($quoteStrategy)->addColumn('User.id_role')->from('users', 'User')->setLimit(1);
        $query  = Query::create($quoteStrategy)->addColumn($subqueryColumn, 'first_role')->from('roles');
        $this->assertEquals("SELECT ( SELECT `User`.`id_role` FROM `users` as `User` LIMIT 1 ) as `first_role` FROM `roles`", $query->createSql());
    }

    /**
     *
     * @test
     * @dataProvider getProviders
     */
    public function subqueryTable($quoteStrategy)
    {
        $subqueryTable = Query::create($quoteStrategy)->from('users', 'UserView');
        $subqueryTable->where()->add('UserView.id_user', '5-9', Criterion::RANGE);

        $query  = Query::create($quoteStrategy)->from($subqueryTable, 'User');
        $this->assertEquals("SELECT * FROM ( SELECT * FROM `users` as `UserView` WHERE ( `UserView`.`id_user` IN (5, 6, 7, 8, 9) ) ) as `User`", $query->createSql());
    }

    /**
     *
     * @return array
     */
    public function getProviders(){
        return array(
            array(new SimpleQuoteStrategy()),
            array($this->getZendDbQuoteStrategy()),
        );
    }


}