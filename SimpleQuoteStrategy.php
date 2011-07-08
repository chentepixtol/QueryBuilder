<?php


class SimpleQuoteStrategy implements QuoteStrategy
{

	private $separator = "'";
	private $implodeGlue = ',';

	/**
	 * (non-PHPdoc)
	 * @see QuoteStrategy::quote()
	 */
    public function quote($value)
    {
    	$this->separator = ( is_int($value) || is_float($value) )? null : "'";
    	$this->implodeGlue = ', ';
        return $this->_quote($value);
    }



    /* (non-PHPdoc)
	 * @see QuoteStrategy::quoteColumn()
	 */
	public function quoteColumn($value)
	{
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
	public function quoteTable($value) {
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
    	return is_array($value) ? implode($this->implodeGlue, array_map(array($this, '_quote'), $value)) : "{$this->separator}{$value}{$this->separator}";
    }



}
