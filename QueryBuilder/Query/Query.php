<?php

/**
 *
 * Query
 *
 * @package Query
 * @copyright (c) Vicente Mendoza <chentepixtol@gmail.com>
 * @author chentepixtol
 *
 */
class Query implements SelectCriterion
{

	/**
	 *
	 * @var string
	 */
	const ALL_COLUMNS = '*';
	const ASC = 'ASC';
	const DESC = 'DESC';

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
	 * El limite de filas que regresar� el sql .  <code>0</code> significa que regresa todos
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
	 * Columnas por las que se ordenar� el resultado
	 * @var mixed
	 */
	protected $orderByColumns = array ();

	/**
	 * Columnas por las que se agrupar�n los resultados
	 * @var mixed
	 */
	protected $groupByColumns = array ();

	/**
	 *
	 * @var Criteria
	 */
	protected $whereCriteria;

	/**
	 *
	 * @var Criteria
	 */
	protected $havingCriteria;

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
	protected $groupSql;

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
	 * Construct
	 * @param QuoteStrategy $quoteStrategy
	 */
	public function __construct(QuoteStrategy $quoteStrategy = null)
	{
		$this->whereCriteria = new Criteria($this);
		$this->havingCriteria = new Criteria($this);
		$this->setQuoteStrategy($quoteStrategy ? $quoteStrategy : new SimpleQuoteStrategy());
	}

	/**
	 *
	 * Factory
	 * @param QuoteStrategy $quoteStrategy
	 * @return Query
	 */
	public static function create(QuoteStrategy $quoteStrategy = null){
		return new static($quoteStrategy);
	}

	/**
	 *
	 *
	 * @param string $table
	 * @param strinf $type
	 * @return Criteria
	 */
	public function joinOn($table, $type = Criterion::JOIN)
	{
		$on = new Criteria($this);
		$on->setQuoteStrategy($this->quoteStrategy);
		$this->joinSql = null;
		$this->joins[$table] = array(
			'table' => $table,
			'type' => $type,
			'on' => $on,
			'using' => null,
		);
		return $on;
	}

	/**
	 *
	 * @param string $table
	 * @param string $usingColumn
	 * @param string $type
	 */
	public function joinUsing($table, $usingColumn, $type = Criterion::JOIN)
	{
		$this->joinSql = null;
		$this->joins[$table] = array(
			'table' => $table,
			'type' => $type,
			'using' => $usingColumn,
		);
		return $this;
	}

	/**
	 *
	 * @param string $table
	 * @return Criteria
	 */
	public function innerJoinOn($table){
		return $this->joinOn($table, Criterion::INNER_JOIN);
	}

	/**
	 *
	 * @param string $table
	 * @param string $on
	 */
	public function innerJoinUsing($table, $usingColumn){
		return $this->joinUsing($table, $usingColumn, Criterion::INNER_JOIN);
	}

	/**
	 *
	 * @param string $table
	 * @return Criteria
	 */
	public function leftJoinOn($table){
		return $this->joinOn($table, Criterion::LEFT_JOIN);
	}

	/**
	 *
	 * @param string $table
	 * @param string $on
	 */
	public function leftJoinUsing($table, $usingColumn){
		return $this->joinUsing($table, $usingColumn, Criterion::LEFT_JOIN);
	}

	/**
	 *
	 * @param string $table
	 * @return Criteria
	 */
	public function rightJoinOn($table){
		return $this->joinOn($table, Criterion::RIGHT_JOIN);
	}

	/**
	 *
	 * @param string $table
	 * @param string $on
	 */
	public function rightJoinUsing($table, $usingColumn){
		return $this->joinUsing($table, $usingColumn, Criterion::RIGHT_JOIN);
	}

	/**
	 *
	 * @return Query
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
	 * @return Query
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
	 * @param string $from
	 * @return Query
	 */
	public function removeFrom($from = null)
	{
		$this->fromSql = null;
		if( $from ){
			$k = array_search($from, $this->from);
			if( $k !== false ) unset($this->from[$k]);
		}
		else {
			$this->from = array();
		}
		return $this;
	}

	/**
	 *
	 * @param string $column
	 * @return Query
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
	 * @return Query
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
	 * @return Criteria
	 */
	public function where(){
		return $this->whereCriteria;
	}

	/**
	 *
	 * @param string $column
	 * @param mixed $value
	 * @param string $comparison
	 * @param string $mutatorColumn
	 * @param string $mutatorValue
	 * @return Query
	 */
	public function whereAdd($column, $value, $comparison = Criterion::AUTO, $mutatorColumn = null, $mutatorValue = null){
		$this->whereCriteria->add($column, $value, $comparison, $mutatorColumn, $mutatorValue);
		return $this;
	}

	/**
	 * (non-PHPdoc)
	 * @see SelectCriterion::createWhereSql()
	 */
	public function createWhereSql()
	{
		return 'WHERE '.$this->whereCriteria->createSql();
	}

	/**
	 *
	 * @return Criteria
	 */
	public function having(){
		return $this->havingCriteria;
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

		if(empty($this->columns)){
			$sql .= ' '.self::ALL_COLUMNS;
		}

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

		$tables = count($this->from);

		if( 0 === $tables){
			throw new Exception("No se ha definido ninguna tabla en la parte sql del FROM");
		}

		$sql = 'FROM ';
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
			if( $join['using'] ){
				$field = $this->quoteStrategy->quoteColumn($join['using']);
				$sql .= " USING( {$field} ) ";
			}
			else{
				if( $join['on'] instanceof Criteria ){
					$sql .= " ON{$join['on']->createSql()}";
				}
			}

		}
		$this->joinSql = trim($sql);
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
			$sql .= "GROUP BY ";
			$columns = array_map(array($this->quoteStrategy, 'quoteColumn'), $this->groupByColumns);
			$sql .= implode(', ', $columns);
		}
		$this->groupSql = $sql;
		return $this->groupSql;
	}

	/* (non-PHPdoc)
	 * @see SelectCriterion::createHavingSql()
	 */
	public function createHavingSql()
	{
		if( $this->havingCriteria->isEmpty() ){
			return '';
		}
		return "HAVING ".$this->havingCriteria->createSql();
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
			$sql = "ORDER BY ";
			$columns = array_map(array($this, '_quoteOrder'), $this->orderByColumns);
			$sql .= implode(', ',  $columns);
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
			$this->createGroupSql().
			$this->createHavingSql().' '.
			$this->createOrderSql().' '.
			$this->createLimitSql();
	}

	/**
	 *
	 * @return string
	 */
	public function createBeautySql(){
		$find = array('FROM', 'INNER JOIN', 'LEFT JOIN', 'RIGHT JOIN', 'WHERE', 'GROUP', 'HAVING', 'ORDER', 'LIMIT');
		$replace = array("\nFROM", "\nINNER JOIN", "\nLEFT JOIN", "\nRIGHT JOIN", "\nWHERE", "\nGROUP", "\nHAVING", "\nORDER", "\nLIMIT");
		return str_replace($find, $replace, $this->createSql());
	}

	/* (non-PHPdoc)
	 * @see Criterion::setQuoteStrategy()
	 *
	 */
	public function setQuoteStrategy(QuoteStrategy $quoteStrategy)
	{
		$this->quoteStrategy = $quoteStrategy;
		$this->whereCriteria->setQuoteStrategy($quoteStrategy);
		$this->havingCriteria->setQuoteStrategy($quoteStrategy);
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
	 * @return Query
	 */
	public function addGroupBy($groupBy)
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
	 *
	 * order by
	 * @param string $name
	 * @param string $type
	 * @return Query
	 */
	public function orderBy($name, $type = Query::ASC)
	{
		$this->orderSql = null;
		$this->orderByColumns [] = array('column' => $name, 'type' => $type);
		return $this;
	}

	/**
	 * Agrega una columna para ordenar de forma ascendente
	 *
	 * @param string $name El nombde de la columna.
	 * @return  Query
	 */
	public function addAscendingOrderBy($name){
		return $this->orderBy($name, Query::ASC);
	}

	/**
	 * Agrega una columna para ordenar de forma descendente
	 *
	 * @param string $name El nombre de la columna
	 * @return Query
	 */
	public function addDescendingOrderBy($name){
		return $this->orderBy($name, Query::DESC);
	}



}