<?php


class SimpleQuoteStrategy implements QuoteStrategy
{

	private $separator = "'";

	/**
	 * (non-PHPdoc)
	 * @see QuoteStrategy::quote()
	 */
    public function quote($value){
    	$this->separator = "'";
        return $this->_quote($value);
    }



    /* (non-PHPdoc)
	 * @see QuoteStrategy::quoteColumn()
	 */
	public function quoteColumn($value) {
		$this->separator = '`';
		return $this->_quote(explode('.', $value));
	}

	/* (non-PHPdoc)
	 * @see QuoteStrategy::quoteTable()
	 */
	public function quoteTable($value) {
		$this->separator = '`';
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
    	return is_array($value) ? implode('.', array_map(array($this, '_quote'), $value)) : "{$this->separator}{$value}{$this->separator}";
    }



}
