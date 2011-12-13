<?php

use Query\ZendDbQuoteStrategy;
use Symfony\Component\ClassLoader\UniversalClassLoader;

require_once 'vendor/Symfony/Component/ClassLoader/UniversalClassLoader.php';

$loader = new UniversalClassLoader();
$loader->registerNamespaces(array(
	'Query'     => 'src/',
));
$loader->register();


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

		if( null == $this->quoteStrategy ){
			$db = Zend_Db::factory('Pdo_Mysql', array(
			    'host'     => '127.0.0.1',
			    'username' => 'sd',
			    'password' => '123',
			    'dbname'   => 'sd_ixe'
			));

			$this->quoteStrategy = new ZendDbQuoteStrategy($db);
		}
		return $this->quoteStrategy;
	}


}

