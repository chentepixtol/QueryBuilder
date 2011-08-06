<?php

namespace Query;

/**
 *
 * Expression
 *
 * @package Query
 * @copyright (c) Vicente Mendoza <chentepixtol@gmail.com>
 * @author chentepixtol
 *
 */
class Expression
{

	/**
	 *
	 * @var string
	 */
	private $expression = '';

	/**
	 *
	 * @param string $expression
	 */
	public function __construct($expression){
		$this->expression = $expression;
	}

	/**
	 *
	 * @return string
	 */
	public function toString(){
		return $this->expression;
	}
}
