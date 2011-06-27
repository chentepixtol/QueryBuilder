<?php

require_once 'QuoteStrategy.php';

class ZendDbQuoteStrategy implements QuoteStrategy
{
	/**
	 *
	 *
	 * @var Zend_Db_Adapter_Abstract
	 */
	private $db;

	public function __construct(Zend_Db_Adapter_Abstract $db){
		$this->db = $db;
	}

	/**
	 *
	 *
	 * @param mixed $value
	 */
    public function quote($value){
    	return $this->db->quote($value);
    }

    /**
	 *
	 *
	 * @param mixed $value
	 */
    public function quoteTable($value){
    	return $this->db->quoteIdentifier($value);
    }

    /**
	 *
	 *
	 * @param mixed $value
	 */
    public function quoteColumn($value){
    	return $this->db->quoteColumnAs($value, null);
    }

}
