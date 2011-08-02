<?php

require_once 'QueryBuilder/Query/Criterion.php';
require_once 'QueryBuilder/Query/CriterionComposite.php';
require_once 'QueryBuilder/Query/SelectCriterion.php';
require_once 'QueryBuilder/Query/QuoteStrategy.php';

require_once 'QueryBuilder/Query/Range.php';
require_once 'QueryBuilder/Query/ConditionalCriterion.php';
require_once 'QueryBuilder/Query/AutoConditionalCriterion.php';
require_once 'QueryBuilder/Query/ConditionalComposite.php';

require_once 'QueryBuilder/Query/Criteria.php';
require_once 'QueryBuilder/Query/SimpleQuoteStrategy.php';
require_once 'QueryBuilder/Query/ZendDbQuoteStrategy.php';
require_once 'QueryBuilder/Query/Query.php';


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

