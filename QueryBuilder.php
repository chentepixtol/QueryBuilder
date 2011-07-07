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
class QueryBuilder implements SelectCriterion
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
	public function from($table, $alias = null){
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
	public function add($column, $value, $comparison = Criterion::EQUAL, $mutatorColumn = null, $mutatorValue = null)
	{
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
			$sql .= $this->quoteStrategy->quoteColumn($column);
			if( is_string($alias) ) $sql.= ' as '. $this->quoteStrategy->quoteColumn($alias);
			$i++;
			if( $i != $n ) $sql.= ', ';
		}

		return $sql;
	}

	/* (non-PHPdoc)
	 * @see SelectCriterion::createFromSql()
	 */
	public function createFromSql() {

		$sql = 'FROM ';
		$tables = count($this->from);
		$i = 1;
		foreach ($this->from as $alias => $table){
			$alias = is_string($alias) ? ' as '.$this->quoteStrategy->quoteTable($alias) : '';
			$sql .= $this->quoteStrategy->quoteTable($table).$alias;
			if($tables != $i) $sql.= ', ';
			$i++;
		}

		return $sql;
	}

	/* (non-PHPdoc)
	 * @see SelectCriterion::createJoinSql()
	 */
	public function createJoinSql() {
		foreach ($this->joins as $join){
			$sql .= $join['type'].' '.  $this->quoteStrategy->quoteTable($join['table']);
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
			$columns = array_map(array($this->quoteStrategy, 'quoteColumn'), $this->groupByColumns);
			$sql .= implode ( ',', $columns);
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
			$columns = array_map(array($this, '_quoteOrder'), $this->orderByColumns);
			$sql .= implode ( ',',  $columns);
		}
		return $sql;
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
		$this->orderByColumns [] = array('column' => $name, 'type' => 'ASC');
		return $this;
	}

	/**
	 * Agrega una columna para ordenar de forma descendente
	 *
	 * @param string $name El nombre de la columna
	 * @return QueryBuilder
	 */
	public function addDescendingOrderByColumn($name) {
		$this->orderByColumns [] = array('column' => $name, 'type' => 'DESC');
		return $this;
	}



}