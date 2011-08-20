<?php

namespace Query;

/**
 *
 * Columns
 *
 * @package Query
 * @copyright (c) Vicente Mendoza <chentepixtol@gmail.com>
 * @author chentepixtol
 *
 */
class Columns implements Criterion
{

	/**
	 *
	 * Lazy Load
	 * @var string
	 */
	protected $sql;

	/**
	 *
	 * Columns
	 * @var array
	 */
	protected $columns = array();

	/**
	 *
	 * @var QuoteStrategy
	 */
	protected $quoteStrategy;

	/**
	 *
	 * @var string
	 */
	protected $defaultColumn = '*';

	/**
	 *
	 * @param string $column
	 * @return Columns
	 */
	public function removeColumn($column = null)
	{
		$this->sql = null;
		if( $column ){
			$k = array_search($column, $this->columns);
			if( $k !== false ) unset($this->columns[$k]);
		}
		else {
			$this->columns = array();
		}
		return $this;
	}

	/**
	 *
	 * addColumns
	 * @param array $columns
	 * @return Columns
	 */
	public function addColumns($columns)
	{
		foreach ($columns as $alias => $column){
			$this->addColumn($column, $alias);
		}
		return $this;
	}

	/**
	 *
	 * @param string $column
	 * @param string $alias
	 * @return Columns
	 */
	public function addColumn($column, $alias = null)
	{
		$this->sql = null;
		if( is_string($alias) )
			$this->columns[$alias] = $column;
		else
			$this->columns[] = $column;
		return $this;
	}

	/* (non-PHPdoc)
	 * @see Criterion::createSql()
	 */
	public function createSql()
	{
		if( null !== $this->sql ){
			return $this->sql;
		}

		$sql = '';
		if( empty($this->columns) ){
			$sql .= ' '.$this->quoteStrategy->quoteColumn($this->getDefaultColumn());
		}

		$n = count($this->columns);
		$i = 0;
		foreach ($this->columns as $alias => $column){
			$sql .= ' ' . $this->quoteStrategy->quoteColumn($column);
			if( is_string($alias) ) $sql.= ' as '. $this->quoteStrategy->quoteColumn($alias);
			$i++;
			if( $i != $n ) $sql.= ',';
		}

		$this->sql = $sql;
		return $this->sql;
	}

	/**
	 *
	 * @param QuoteStrategy $quoteStrategy
	 */
	public function setQuoteStrategy(QuoteStrategy $quoteStrategy){
		$this->quoteStrategy = $quoteStrategy;
	}

	/**
	 *
	 * @return string
	 */
	public function getDefaultColumn(){
		return $this->defaultColumn;
	}

	/**
	 *
	 * @param string $defaultColumn
	 * @return Query
	 */
	public function setDefaultColumn($defaultColumn)
	{
		$this->defaultColumn = $defaultColumn;
		return $this;
	}

}

