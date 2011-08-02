<?php

/**
 *
 * QuoteStrategy
 *
 * @package Query
 * @copyright (c) Vicente Mendoza <chentepixtol@gmail.com>
 * @author chentepixtol
 *
 */
interface QuoteStrategy
{

	/**
	 *
	 *
	 * @param mixed $value
	 */
    public function quote($value);

    /**
	 *
	 *
	 * @param mixed $value
	 */
    public function quoteTable($value);

    /**
	 *
	 *
	 * @param mixed $value
	 */
    public function quoteColumn($value);

}
