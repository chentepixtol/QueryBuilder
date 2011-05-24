<?php

require_once 'Criterion.php';
require_once 'CriterionComposite.php';
require_once 'ConditionalComposite.php';
require_once 'ConditionalCriterion.php';
require_once 'QuoteStrategy.php';
require_once 'SimpleQuoteStrategy.php';
require_once 'SelectCriterion.php';

/**
 *
 * QueryBuilder
 * @author chente
 *
 */
class QueryBuilder implements SelectCriterion
{

	/**
	 *
	 * Construct
	 */
	public function __construct(){
		$this->currentWhereComposite = $this->whereComposite = new ConditionalComposite();
		$simpleQuoteStrategy = new SimpleQuoteStrategy();
		$this->setQuoteStrategy($simpleQuoteStrategy);
		$this->whereComposite->setQuoteStrategy($simpleQuoteStrategy);
	}

	/**
	 *
	 * @var QuoteStrategy
	 */
	protected $quoteStrategy;

	/**
	 * @var string
	 */
	protected $from;

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
	 *
	 * @param string $table
	 * @param string $on
	 * @return QueryBuilder
	 */
	public function join($table, $on = null, $type = Criterion::INNER_JOIN, $using = null)
	{
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
		if( isset($this->joins[$table]) )
			unset($this->joins[$table]);
		return $this;
	}

	/**
	 *
	 * Enter description here ...
	 * @param unknown_type $table
	 */
	public function from($table){
		$this->from = $table;
		return $this;
	}

	/**
	 *
	 * @param string $column
	 * @return QueryBuilder
	 */
	public function removeColumn($column = null){
		if( $column ){
			$k = array_search($column, $this->columns);
			if( $k ) unset($this->columns[$k]);
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
	public function addColumn($column, $alias = null){
		if($alias)
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
	public function add($column, $value, $comparison = Criterion::EQUAL, $mutatorColumn = null, $mutatorValue = null)
	{
		$criterion = new ConditionalCriterion($column, $value, $comparison, $mutatorColumn, $mutatorValue);
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
	public function end(){
		$this->currentWhereComposite = $this->currentWhereComposite->getParent();
		return $this;
	}

	/**
	 * (non-PHPdoc)
	 * @see SelectCriterion::createWhereSql()
	 */
	public function createWhereSql(){
		return 'WHERE '.$this->whereComposite->createSql();
	}

	/* (non-PHPdoc)
	 * @see SelectCriterion::createSelectSql()
	 */
	public function createSelectSql() {
		$sql = 'SELECT ';

		if(empty($this->columns)) $sql .= '* ';

		$n = count($this->columns);
		$i = 0;
		foreach ($this->columns as $alias => $column){
			$sql .= $column;
			if( is_string($alias) ) $sql.= ' as '. $alias;
			$i++;
			if( $i != $n ) $sql.= ', ';
		}

		return $sql;
	}

	/* (non-PHPdoc)
	 * @see SelectCriterion::createFromSql()
	 */
	public function createFromSql() {
		return 'FROM '. $this->from;
	}

	/* (non-PHPdoc)
	 * @see SelectCriterion::createJoinSql()
	 */
	public function createJoinSql() {
		foreach ($this->joins as $join){
			$sql .= $join['type'].' '. $join['table'];
			if( $join['using'] )
				$sql .= " USING ({$join['using']})";
			else
				$sql .= " ON ({$join['on']})";
		}
		return $sql;
	}

	/* (non-PHPdoc)
	 * @see SelectCriterion::createGroupSql()
	 */
	public function createGroupSql() {
		$sql = '';
		if (count ( $this->groupByColumns )) {
			$sql .= "GROUP BY  ";
			$sql .= implode ( ',', $this->groupByColumns );
		}
		return $sql;
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
	public function createOrderSql() {
		$sql = '';
		if ( count ($this->orderByColumns ) ) {
			$sql = "ORDER BY  ";
			$sql .= implode ( ',', $this->orderByColumns );
		}
		return $sql;
	}

	/* (non-PHPdoc)
	 * @see SelectCriterion::createLimitSql()
	 */
	public function createLimitSql() {
		$sql = '';
		if ($this->limit != 0) {
			$sql .= "LIMIT " . $this->limit;
		}
		if ($this->offset != 0) {
			$sql .= " OFFSET " . $this->offset;
		}
		return $sql;
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
	public function setQuoteStrategy(QuoteStrategy $quoteStrategy) {
		$this->quoteStrategy = $quoteStrategy;
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
	public function setLimit($limit) {
		$this->limit = $limit;
		return $this;
	}

	/**
	 * @param int $offset
	 */
	public function setOffset($offset) {
		$this->offset = $offset;
		return $this;
	}

	/**
	 * GUarda una columna para ordenar los resultados
	 * @param string $groupBy
	 * @return QueryBuilder
	 */
	public function addGroupByColumn($groupBy) {
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
	public function addAscendingOrderByColumn($name) {
		$this->orderByColumns [] = $name . ' ASC';
		return $this;
	}

	/**
	 * Agrega una columna para ordenar de forma descendente
	 *
	 * @param string $name El nombre de la columna
	 * @return QueryBuilder
	 */
	public function addDescendingOrderByColumn($name) {
		$this->orderByColumns [] = $name . ' DESC';
		return $this;
	}



}