<?php

namespace Query;

/**
 *
 * SimpleQuoteStrategy
 *
 * @package Query
 * @copyright (c) Vicente Mendoza <chentepixtol@gmail.com>
 * @author chentepixtol
 *
 */
class SimpleQuoteStrategy implements QuoteStrategy
{

	/**
	 *
	 * separator
	 * @var string
	 */
	private $separator = "'";

	/**
	 *
	 * separator glue
	 * @var string
	 */
	private $implodeGlue = ',';

	/**
	 * (non-PHPdoc)
	 * @see QuoteStrategy::quote()
	 */
    public function quote($value)
    {
    	if( $value instanceof Criterion ){
    		$value = new Expression('( '.$value->createSql().' )');
    	}
    	if( $value instanceof Expression ){
    		return $value->toString();
    	}
    	$this->separator = ( is_int($value) || is_float($value) )? null : "'";
    	$this->implodeGlue = ', ';
        return $this->_quote($value);
    }

    /* (non-PHPdoc)
	 * @see QuoteStrategy::quoteColumn()
	 */
	public function quoteColumn($value)
	{
		if( $value instanceof Criterion ){
    		$value = new Expression('( '.$value->createSql().' )');
    	}
		if( $value instanceof Expression ){
    		return $value->toString();
    	}
		$this->separator = '`';
		$this->implodeGlue = '.';
		if( is_array($value) ){
			$column = $this->_quote($value);
		}
		else{
			$column = $this->_quote(explode('.', $value));
		}
		$column = str_replace('`*`', '*', $column);
		return $column;
	}

	/* (non-PHPdoc)
	 * @see QuoteStrategy::quoteTable()
	 */
	public function quoteTable($value)
	{
		if( $value instanceof Criterion ){
    		$value = new Expression('( '.$value->createSql().' )');
    	}
		if( $value instanceof Expression ){
    		return $value->toString();
    	}
		$this->separator = '`';
		$this->implodeGlue = '.';
		return $this->_quote(explode('.', $value));
	}

	/**
     *
     * Enter description here ...
     * @param string $value
     * @return string
     */
    protected function _quote($value)
    {
    	$oldSeparator = $this->separator;
    	if( is_int($value) || is_float($value) ){
    		$this->separator = null;
    	}

    	$return = is_array($value) ? implode($this->implodeGlue, array_map(array($this, '_quote'), $value)) : "{$this->separator}{$value}{$this->separator}";
    	$this->separator = $oldSeparator;
    	return $return;
    }

}
