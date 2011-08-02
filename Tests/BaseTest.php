<?php

require_once 'Application/Query/Criterion.php';
require_once 'Application/Query/CriterionComposite.php';
require_once 'Application/Query/SelectCriterion.php';
require_once 'Application/Query/QuoteStrategy.php';

require_once 'Application/Query/Range.php';
require_once 'Application/Query/ConditionalCriterion.php';
require_once 'Application/Query/AutoConditionalCriterion.php';
require_once 'Application/Query/ConditionalComposite.php';

require_once 'Application/Query/Criteria.php';
require_once 'Application/Query/SimpleQuoteStrategy.php';
require_once 'Application/Query/ZendDbQuoteStrategy.php';
require_once 'Application/Query/Query.php';


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

