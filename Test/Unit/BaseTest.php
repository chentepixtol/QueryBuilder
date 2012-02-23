<?php

namespace Test\Unit;

use Query\ZendDbQuoteStrategy;
use Query\SimpleQuoteStrategy;

/**
 *
 * @author chente
 *
 */
abstract class BaseTest extends \PHPUnit_Framework_TestCase
{

    /**
     *
     * @var QuoteStrategy
     */
    private $quoteStrategy;

    /**
     *
     * @return QuoteStrategy
     */
    public function getZendDbQuoteStrategy()
    {
        if( null == $this->quoteStrategy ){

            $db = \Zend_Db::factory('Pdo_Mysql', array(
                'host'     => '127.0.0.1',
                'username' => 'bender',
                'password' => '123',
                'dbname'   => 'bender',
            ));

            $this->quoteStrategy = new ZendDbQuoteStrategy($db);
        }
        return $this->quoteStrategy;
    }


}

