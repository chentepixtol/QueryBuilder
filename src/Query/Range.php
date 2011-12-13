<?php

namespace Query;

/**
 *
 * Range
 *
 * @package Query
 * @copyright (c) Vicente Mendoza <chentepixtol@gmail.com>
 * @author chentepixtol
 *
 */
class Range
{

	/**
	 *
	 *
	 * @var array
	 */
	protected $array = null;

	/**
	 *
	 *
	 * @var string
	 */
	protected $string = null;

	/**
	 *
	 *
	 * @param array $array
	 */
	public function fromArray($array)
	{
		$this->validateArray($array);
		$this->array = array_unique($array);
		sort($this->array);
	}

	/**
	 *
	 * validate
	 * @param array $array
	 * @throws Exception
	 */
	protected function validateArray($array)
	{
		if( !is_array($array) ){
			throw new Exception("No es un array valido");
		}

		foreach ($array as $i => $value)
		{
			if( is_object($value) || is_array($value) || !is_numeric($value) ){
				throw new Exception("Tipo de dato no permitido");
			}

			if( is_string($value) ){
				if( preg_match('/^[0-9]+$/', $value) ){
					$array[$i] = (int) $value;
				}
				else {
					throw new Exception("Dato Invalido: ".$value);
				}
			}

			if( $value <= 0 ){
				throw new Exception("Los rangos con numeros negativos no pueden generarse");
			}
		}
	}

	/**
	 *
	 *
	 * @param string $string
	 */
	public function fromString($string)
	{
		$string = trim($string);
		if( !empty($string) && !preg_match('/^([0-9]+(\-[0-9]+)?,)*([0-9]+(\-[0-9]+)?)+$/', $string) ){
			throw new Exception("Rango incorrecto: ".$string);
		}
		$this->string = $string;
	}

	/**
	 *
	 * @return array
	 */
	public function toArray()
	{
		if( !empty($this->array) )
			return $this->array;

		if( empty($this->string) )
			return array();

		$ranges = array_map(array($this, '_range'), explode(',', $this->string));
		$array = array();
		foreach ( $ranges as $range ){
			$array = array_merge($array, $range);
		}
		$array = array_unique($array);
		sort($array);
		return $array;
	}

	/**
	 *
	 * @return string
	 */
	public function toString()
	{
		if( !empty($this->string) )
			return $this->string;

		if( empty($this->array) )
			return '';

		$ranges = array();
		$currentRange = array();

		$previousValue = -100000;
		foreach ($this->array as $value)
		{
			if( ( ($previousValue + 1) == $value) ){
				$currentRange[1] = $value;
			}
			else{
				if( !empty($currentRange) ) $ranges[] = $currentRange;
				$currentRange = array($value);
			}

			$previousValue = $value;
		}
		if( !empty($currentRange) ) $ranges[] = $currentRange;

		$pieces = array_map(array($this, '_implode'), $ranges);
		return implode(',', $pieces);
	}

	/**
	 *
	 *
	 * @param array $elems
	 * @return string
	 */
	public function _implode($elems){
		return implode('-', $elems);
	}

	/**
	 *
	 *
	 * @param string $object
	 * @return array
	 */
	public function _range($object){
		$array = explode('-', $object);
		if( count($array) == 2 )
		{
			return range($array[0], $array[1]);
		}else{
			return array( (int) $object);
		}
	}

}