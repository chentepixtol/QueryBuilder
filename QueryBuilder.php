<?php

require_once 'Criterion.php';
require_once 'CriterionComposite.php';
require_once 'ConditionalComposite.php';
require_once 'ConditionalCriterion.php';
require_once 'QuoteStrategy.php';
require_once 'SimpleQuoteStrategy.php';
require_once 'SelectCriterion.php';
require_once 'AutoConditionalCriterion.php';

/**
 *
 * QueryBuilder
 * @author chente
 *
 */
class Query implements SelectCriterion
{

	/**
	 *
	 * Construct
	 * @param QuoteStrategy $quoteStrategy
	 */
	public function __construct(QuoteStrategy $quoteStrategy = null)
	{
		$this->currentWhereComposite = $this->whereComposite = new ConditionalComposite();
		if( null == $quoteStrategy ){
			$quoteStrategy = new SimpleQuoteStrategy();
		}
		$this->setQuoteStrategy($quoteStrategy);
	}

	/**
	 *
	 * @var QuoteStrategy
	 */
	protected $quoteStrategy;

	/**
	 * @var array
	 */
	protected $from = array();

	/**
	 * @var array
	 */
	protected $columns = array();

	/**
	 * @var array
	 */
	protected $joins = array();

	/**
	 * El limite de filas que regresará el sql .  <code>0</code> significa que regresa todos
	 * rows.
	 * @var int $limit
	 */
	protected $limit = 0;

	/**
	 * Para comenzar a desplegar los resultados en una fila diferente a la primera
	 * @var int $offset
	 */
	protected $offset = 0;

	/**
	 * Columnas por las que se ordenará el resultado
	 * @var mixed
	 */
	protected $orderByColumns = array ();

	/**
	 * Columnas por las que se agruparán los resultados
	 * @var mixed
	 */
	protected $groupByColumns = array ();

	/**
	 *
	 * Enter description here ...
	 * @var ConditionalComposite
	 */
	protected $whereComposite;

	/**
	 *
	 * Enter description here ...
	 * @var ConditionalComposite
	 */
	protected $currentWhereComposite;

	/**
	 *
	 * Lazy Load
	 * @var string
	 */
	protected $selectSql;

	/**
	 *
	 * Lazy Load
	 * @var string
	 */
	protected $fromSql;

	/**
	 *
	 * Lazy Load
	 * @var string
	 */
	protected $joinSql;

	/**
	 *
	 * Lazy Load
	 * @var string
	 */
	protected $whereSql;

	/**
	 *
	 * Lazy Load
	 * @var string
	 */
	protected $groupSql;

	/**
	 *
	 * Lazy Load
	 * @var string
	 */
	protected $havingSql;

	/**
	 *
	 * Lazy Load
	 * @var string
	 */
	protected $orderSql;

	/**
	 *
	 * Lazy Load
	 * @var string
	 */
	protected $limitSql;

	/**
	 *
	 *
	 * @param string $table
	 * @param string $on
	 * @return QueryBuilder
	 */
	public function join($table, $on = null, $type = Criterion::INNER_JOIN, $using = null)
	{
		$this->joinSql = null;
		$this->joins[$table] = array(
			'table' => $table,
			'type' => $type,
			'on' => $on,
			'using' => $using,
		);
		return $this;
	}

	/**
	 *
	 * @return QueryBuilder
	 */
	public function removeJoins()
	{
		$this->joinSql = null;
		$this->joins = array();
		return $this;
	}

	/**
	 *
	 * Enter description here ...
	 * @param unknown_type $table
	 * @return QueryBuilder
	 */
	public function removeJoin($table)
	{
		$this->joinSql = null;
		if( isset($this->joins[$table]) )
			unset($this->joins[$table]);
		return $this;
	}

	/**
	 *
	 * Enter description here ...
	 * @param unknown_type $table
	 */
	public function from($table, $alias = null)
	{
		$this->fromSql = null;
		if( is_string($alias) ){
			$this->from[$alias] = $table;
		}else{
			$this->from[] = $table;
		}

		return $this;
	}

	/**
	 *
	 * @param string $column
	 * @return QueryBuilder
	 */
	public function removeColumn($column = null)
	{
		$this->selectSql = null;
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
	 * @param string $column
	 * @param string $alias
	 * @return QueryBuilder
	 */
	public function addColumn($column, $alias = null)
	{
		$this->selectSql = null;
		if( is_string($alias) )
			$this->columns[$alias] = $column;
		else
			$this->columns[] = $column;
		return $this;
	}

	/**
	 *
	 * Enter description here ...
	 * @param string $column
	 * @param mixed $value
	 * @param string $comparison
	 * @param string $mutatorColumn
	 * @param string $mutatorValue
	 * @return QueryBuilder
	 */
	public function add($column, $value, $comparison = Criterion::AUTO, $mutatorColumn = null, $mutatorValue = null)
	{
		$this->whereSql = null;
		$criterion = ConditionalCriterion::factory($column, $value, $comparison, $mutatorColumn, $mutatorValue);
		$this->currentWhereComposite->addCriterion($criterion);
		return $this;
	}

	/**
	 *
	 * @return QueryBuilder
	 */
	public function setAND()
	{
		if( $this->currentWhereComposite->isEmpty() )
			$this->currentWhereComposite->setOperatorLogic(CriterionComposite::LOGICAL_AND);
		elseif( $this->currentWhereComposite->isLogicalOR() ){
			$composite = new ConditionalComposite();
			$this->currentWhereComposite->addCriterion($composite);
			$this->currentWhereComposite = $composite;
		}

		return $this;
	}

	/**
	 *
	 * @return QueryBuilder
	 */
	public function setOR()
	{
		if( $this->currentWhereComposite->isEmpty() )
			$this->currentWhereComposite->setOperatorLogic(CriterionComposite::LOGICAL_OR);
		elseif( $this->currentWhereComposite->isLogicalAND() ){
			$composite = new ConditionalComposite(CriterionComposite::LOGICAL_OR);
			$this->currentWhereComposite->addCriterion($composite);
			$this->currentWhereComposite = $composite;
		}
		return $this;
	}

	/**
	 *
	 * @return QueryBuilder
	 */
	public function end()
	{
		$this->currentWhereComposite = $this->currentWhereComposite->getParent();
		return $this;
	}

	/**
	 * (non-PHPdoc)
	 * @see SelectCriterion::createWhereSql()
	 */
	public function createWhereSql()
	{
		if( null !== $this->whereSql ){
			return $this->whereSql;
		}
		$this->whereSql = 'WHERE '.$this->whereComposite->createSql();
		return $this->whereSql;
	}

	/* (non-PHPdoc)
	 * @see SelectCriterion::createSelectSql()
	 */
	public function createSelectSql()
	{
		if( null !== $this->selectSql ){
			return $this->selectSql;
		}

		$sql = 'SELECT';

		if(empty($this->columns)) $sql .= ' *';

		$n = count($this->columns);
		$i = 0;
		foreach ($this->columns as $alias => $column){
			$sql .= ' ' . $this->quoteStrategy->quoteColumn($column);
			if( is_string($alias) ) $sql.= ' as '. $this->quoteStrategy->quoteColumn($alias);
			$i++;
			if( $i != $n ) $sql.= ',';
		}

		$this->selectSql = $sql;
		return $this->selectSql;
	}

	/* (non-PHPdoc)
	 * @see SelectCriterion::createFromSql()
	 */
	public function createFromSql()
	{
		if( null !== $this->fromSql ){
			return $this->fromSql;
		}

		$sql = 'FROM ';
		$tables = count($this->from);
		$i = 1;
		foreach ($this->from as $alias => $table){
			$alias = is_string($alias) ? ' as '.$this->quoteStrategy->quoteTable($alias) : '';
			$sql .= $this->quoteStrategy->quoteTable($table).$alias;
			if($tables != $i) $sql.= ', ';
			$i++;
		}
		$this->fromSql = $sql;
		return $this->fromSql;
	}

	/* (non-PHPdoc)
	 * @see SelectCriterion::createJoinSql()
	 */
	public function createJoinSql()
	{
		if( null !== $this->joinSql ){
			return $this->joinSql;
		}

		$sql = '';
		foreach ($this->joins as $join){
			$sql .= $join['type'].' '.  $this->quoteStrategy->quoteTable($join['table']);
			if( $join['using'] )
				$sql .= " USING ({$join['using']})";
			else
				$sql .= " ON ({$join['on']})";
		}
		$this->joinSql = $sql;
		return $this->joinSql;
	}

	/* (non-PHPdoc)
	 * @see SelectCriterion::createGroupSql()
	 */
	public function createGroupSql()
	{
		if( null !== $this->groupSql ){
			return $this->groupSql;
		}

		$sql = '';
		if (count ( $this->groupByColumns )) {
			$sql .= "GROUP BY  ";
			$columns = array_map(array($this->quoteStrategy, 'quoteColumn'), $this->groupByColumns);
			$sql .= implode ( ',', $columns);
		}
		$this->groupSql = $sql;
		return $this->groupSql;
	}

	/* (non-PHPdoc)
	 * @see SelectCriterion::createHavingSql()
	 */
	public function createHavingSql() {
		// TODO Auto-generated method stub
	}

	/* (non-PHPdoc)
	 * @see SelectCriterion::createOrderSql()
	 */
	public function createOrderSql()
	{
		if( null !== $this->orderSql ){
			return $this->orderSql;
		}

		$sql = '';
		if ( count ($this->orderByColumns ) ) {
			$sql = "ORDER BY  ";
			$columns = array_map(array($this, '_quoteOrder'), $this->orderByColumns);
			$sql .= implode ( ',',  $columns);
		}
		$this->orderSql = $sql;
		return $this->orderSql;
	}

	/**
	 *
	 * Enter description here ...
	 * @param array $orderArray
	 * @return string
	 */
	protected function _quoteOrder($orderArray){
		return $this->quoteStrategy->quoteColumn($orderArray['column']) .' '. $orderArray['type'];
	}


	/* (non-PHPdoc)
	 * @see SelectCriterion::createLimitSql()
	 */
	public function createLimitSql()
	{
		if( null !== $this->limitSql ){
			return $this->limitSql;
		}

		$sql = '';
		if ($this->limit != 0) {
			$sql .= "LIMIT " . $this->limit;
		}
		if ($this->offset != 0) {
			$sql .= " OFFSET " . $this->offset;
		}
		$this->limitSql = $sql;
		return $this->limitSql;
	}

	/* (non-PHPdoc)
	 * @see Criterion::createSql()
	 */
	public function createSql() {
		return $this->createSelectSql().' '.
			$this->createFromSql().' '.
			$this->createJoinSql().' '.
			$this->createWhereSql().' '.
			$this->createGroupSql().' '.
			$this->createHavingSql().' '.
			$this->createOrderSql().' '.
			$this->createLimitSql();
	}

	/* (non-PHPdoc)
	 * @see Criterion::setQuoteStrategy()
	 *
	 */
	public function setQuoteStrategy(QuoteStrategy $quoteStrategy)
	{
		$this->quoteStrategy = $quoteStrategy;
		$this->currentWhereComposite->setQuoteStrategy($quoteStrategy);
		$this->whereComposite->setQuoteStrategy($quoteStrategy);
		return $this;
	}

	/**
	 * @return the $quoteStrategy
	 */
	public function getQuoteStrategy() {
		return $this->quoteStrategy;
	}

	/**
	 * @return int
	 */
	public function getLimit() {
		return $this->limit;
	}

	/**
	 * @return int
	 */
	public function getOffset() {
		return $this->offset;
	}

	/**
	 * @param int $limit
	 */
	public function setLimit($limit)
	{
		$this->limitSql = null;
		$this->limit = $limit;
		return $this;
	}

	/**
	 * @param int $offset
	 */
	public function setOffset($offset)
	{
		$this->limitSql = null;
		$this->offset = $offset;
		return $this;
	}

	/**
	 * GUarda una columna para ordenar los resultados
	 * @param string $groupBy
	 * @return QueryBuilder
	 */
	public function addGroupByColumn($groupBy)
	{
		$this->groupSql = null;
		$this->groupByColumns [] = $groupBy;
		return $this;
	}

	/**
	 * @return array
	 */
	public function getGroupByColumns() {
		return $this->groupByColumns;
	}

	/**
	 * Agrega una columna para ordenar de forma ascendente
	 *
	 * @param string $name El nombde de la columna.
	 * @return  QueryBuilder
	 */
	public function addAscendingOrderByColumn($name)
	{
		$this->orderSql = null;
		$this->orderByColumns [] = array('column' => $name, 'type' => 'ASC');
		return $this;
	}

	/**
	 * Agrega una columna para ordenar de forma descendente
	 *
	 * @param string $name El nombre de la columna
	 * @return QueryBuilder
	 */
	public function addDescendingOrderByColumn($name)
	{
		$this->orderSql = null;
		$this->orderByColumns [] = array('column' => $name, 'type' => 'DESC');
		return $this;
	}



}