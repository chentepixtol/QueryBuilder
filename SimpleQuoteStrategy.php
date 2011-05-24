<?php


class SimpleQuoteStrategy implements QuoteStrategy
{

	/**
	 * (non-PHPdoc)
	 * @see QuoteStrategy::quote()
	 */
    public function quote($value){
        return is_array($value) ? array_map(array($this, '_quote'), $value) : $this->_quote($value);
    }

    /**
     *
     * Enter description here ...
     * @param string $value
     * @return string
     */
    protected function _quote($value)
    {
    	return "`{$value}`";
    }

}
