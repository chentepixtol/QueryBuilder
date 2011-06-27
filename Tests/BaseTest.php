<?php

require_once 'QueryBuilder.php';

/**
 *
 * @author chente
 *
 */
abstract class BaseTest extends PHPUnit_Framework_TestCase
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
		require_once 'Zend/Db.php';
		require_once 'ZendDbQuoteStrategy.php';

		if( null == $this->quoteStrategy ){
			$db = Zend_Db::factory('Pdo_Mysql', array(
			    'host'     => '127.0.0.1',
			    'username' => 'user',
			    'password' => '123',
			    'dbname'   => 'database'
			));

			$this->quoteStrategy = new ZendDbQuoteStrategy($db);
		}
		return $this->quoteStrategy;
	}


}

