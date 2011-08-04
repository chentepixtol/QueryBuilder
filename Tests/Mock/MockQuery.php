<?php

use Query\Query;

/**
 *
 *
 * @author chente
 *
 */
class MockQuery extends Query
{

	/**
	 * (non-PHPdoc)
	 * @see Query.Query::createCriteria()
	 */
	protected function createCriteria(){
		return new MockCriteria($this);
	}

}