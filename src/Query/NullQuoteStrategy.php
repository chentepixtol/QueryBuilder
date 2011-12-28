<?php

namespace Query;

/**
 *
 * NullQuoteStrategy
 *
 * @package Query
 * @copyright (c) Vicente Mendoza <chentepixtol@gmail.com>
 * @author chentepixtol
 *
 */
class NullQuoteStrategy implements QuoteStrategy
{

    /**
     * (non-PHPdoc)
     * @see QuoteStrategy::quote()
     */
    public function quote($value)
    {
        return $value;
    }

    /* (non-PHPdoc)
     * @see QuoteStrategy::quoteColumn()
     */
    public function quoteColumn($value)
    {
        return $value;
    }

    /* (non-PHPdoc)
     * @see QuoteStrategy::quoteTable()
     */
    public function quoteTable($value)
    {
        return $value;
    }

}
