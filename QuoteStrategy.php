<?php


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
