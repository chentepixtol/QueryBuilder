<?php

namespace Query;

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
     * @var Columns
     */
    protected $columns;

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
     * @var string
     */
    protected $intoSql;

    /**
     *
     * @var string
     */
    protected $filename;

    /**
     *
     * @var string
     */
    protected $terminated;

    /**
     *
     * @var string
     */
    protected $enclosed;

    /**
     *
     * @var string
     */
    protected $escaped;

    /**
     *
     * @var string
     */
    protected $linesTerminated;

    /**
     *
     * @var array
     */
    protected $parameters = array();

    /**
     *
     * Construct
     * @param QuoteStrategy $quoteStrategy
     */
    public function __construct(QuoteStrategy $quoteStrategy = null)
    {
        $this->whereCriteria = $this->createCriteria();
        $this->havingCriteria = $this->createCriteria();
        $this->columns = new Columns();
        $this->setQuoteStrategy($quoteStrategy ? $quoteStrategy : new SimpleQuoteStrategy());
        $this->init();
    }

    /**
     *
     * Cloning
     */
    public function __clone()
    {
        $this->havingCriteria = clone $this->havingCriteria;
        $this->havingCriteria->setQuery($this);
        $this->whereCriteria = clone $this->whereCriteria;
        $this->whereCriteria->setQuery($this);
        $this->columns = clone $this->columns;
    }

    /**
     *
     * int subclasses
     */
    protected function init(){}

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
     * Factory Method for Criteria
     * @return Criteria
     */
    protected function createCriteria(){
        return new Criteria($this);
    }

    /**
     *
     *
     * @param string $table
     * @param string $alias
     * @param string $type
     * @param mixed $on
     * @param string $using
     */
    protected function join($table, $alias, $type, $on = null, $using = null)
    {
        $this->joinSql = null;
        $key = ( null != $alias )? $alias : $table;
        $this->joins[$key] = array(
            'table' => $table,
            'type' => $type,
            'on' => $on,
            'using' => $using,
            'alias' => $alias,
        );
    }

    /**
     *
     *
     * @param string $table
     * @param strinf $type
     * @param string $alias
     * @return Criteria
     */
    public function joinOn($table, $type = Criterion::JOIN, $alias = null)
    {
        $on = $this->createCriteria();
        $on->setQuoteStrategy($this->quoteStrategy);
        $this->join($table, $alias, $type, $on);
        return $on;
    }

    /**
     *
     * @param string $table
     * @param string $usingColumn
     * @param string $type
     * @param string $alias
     * @return Query
     */
    public function joinUsing($table, $usingColumn, $type = Criterion::JOIN, $alias = null)
    {
        $this->join($table, $alias, $type, null, $usingColumn);
        return $this;
    }

    /**
     *
     * @param string $table
     * @param string $alias
     * @return Criteria
     */
    public function innerJoinOn($table, $alias = null){
        return $this->joinOn($table, Criterion::INNER_JOIN, $alias);
    }

    /**
     *
     * @param string $table
     * @param string $on
     * @param string $alias
     * @return Query
     */
    public function innerJoinUsing($table, $usingColumn, $alias = null){
        return $this->joinUsing($table, $usingColumn, Criterion::INNER_JOIN, $alias);
    }

    /**
     *
     * @param string $table
     * @param string $alias
     * @return Criteria
     */
    public function leftJoinOn($table, $alias = null){
        return $this->joinOn($table, Criterion::LEFT_JOIN, $alias);
    }

    /**
     *
     * @param string $table
     * @param string $on
     * @param string $alias
     * @return Query
     */
    public function leftJoinUsing($table, $usingColumn, $alias = null){
        return $this->joinUsing($table, $usingColumn, Criterion::LEFT_JOIN, $alias);
    }

    /**
     *
     * @param string $table
     * @param string $alias
     * @return Criteria
     */
    public function rightJoinOn($table, $alias = null){
        return $this->joinOn($table, Criterion::RIGHT_JOIN, $alias);
    }

    /**
     *
     * @param string $table
     * @param string $on
     * @param string $alias
     * @return Query
     */
    public function rightJoinUsing($table, $usingColumn, $alias = null){
        return $this->joinUsing($table, $usingColumn, Criterion::RIGHT_JOIN, $alias);
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
     *
     * @param string $table
     * @return Query
     */
    public function removeJoin($table)
    {
        $this->joinSql = null;
        if( isset($this->joins[$table]) ){
            unset($this->joins[$table]);
        }
        return $this;
    }

    /**
     *
     * @param string $table
     * @return boolean
     */
    public function hasJoin($table){
        return isset($this->joins[$table]);
    }

    /**
     *
     *
     * @param string $table
     * @return Query
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
        $this->columns->removeColumn($column);
        return $this;
    }

    /**
     *
     * @param boolean $flag
     * @return Query
     */
    public function distinct($flag = true){
        $this->columns->distinct($flag);
        return $this;
    }

    /**
     *
     * @param mixed $column
     * @return Query
     */
    public function select(){
        $this->columns->addColumns(func_get_args());
        return $this;
    }

    /**
     *
     * addColumns
     * @param array $columns
     * @return Query
     */
    public function addColumns($columns)
    {
        $this->columns->addColumns($columns);
        return $this;
    }

    /**
     *
     * @param string $column
     * @param string $alias
     * @param string $mutator
     * @return Query
     */
    public function addColumn($column, $alias = null, $mutator = null)
    {
        $this->columns->addColumn($column, $alias, $mutator);
        return $this;
    }

    /**
     *
     *
     * @param string $column
     * @return boolean
     */
    public function hasColumn($column){
        return $this->columns->contains($column);
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
        if( $this->whereCriteria->isEmpty() ){
            return '';
        }
        return 'WHERE '.$this->whereCriteria->createSql();
    }

    /**
     * (non-PHPdoc)
     * @see Query.Criterion::contains()
     */
    public function contains($element){
        return $this->whereCriteria->contains($element);
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
        return 'SELECT'.$this->columns->createSql();
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
            $sql .=  $join['type'].' '.  $this->quoteStrategy->quoteTable($join['table']);
            if( $join['alias'] && is_string($join['alias'])  ){
                $sql.=' as '. $this->quoteStrategy->quoteTable($join['alias']);
            }
            if( $join['using'] ){
                $field = $this->quoteStrategy->quoteColumn($join['using']);
                $sql .= " USING( {$field} ) ";
            }
            else{
                if( $join['on'] instanceof Criteria ){
                    $sql .= " ON{$join['on']->createSql()} ";
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
     *
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

    /**
     * (non-PHPdoc)
     * @see Query.SelectCriterion::createIntoSql()
     */
    public function createIntoSql()
    {
        if( null !== $this->intoSql ){
            return $this->intoSql;
        }
        $sql = '';
        if( null != $this->filename ){
            $sql = "INTO OUTFILE '{$this->filename}'";
            if( $this->terminated ){
                $sql .= " FIELDS TERMINATED BY '{$this->terminated}'";
            }
            if( $this->enclosed ){
                $sql .= " ENCLOSED BY '{$this->enclosed}'";
            }
            if( $this->escaped ){
                $sql .= " ESCAPED BY '{$this->escaped}'";
            }
            if( $this->linesTerminated ){
                $sql .= " LINES TERMINATED BY '{$this->linesTerminated}'";
            }
        }

        $this->intoSql = $sql;
        return $this->intoSql;
    }

    /**
     *
     * Bind the values to the query
     * @param array $parameters
     * @return Query
     */
    public function bind($parameters){
        $this->parameters = $parameters;
        return $this;
    }

    /* (non-PHPdoc)
     * @see Criterion::createSql()
     */
    public function createSql()
    {
        $parts = array(
            $this->createSelectSql(),
            $this->createFromSql(),
            $this->createJoinSql(),
            $this->createWhereSql(),
            $this->createGroupSql(),
            $this->createHavingSql(),
            $this->createOrderSql(),
            $this->createLimitSql(),
            $this->createIntoSql()
        );

        $sql = implode(' ', array_filter($parts));

        return $this->replaceParameters($sql);
    }

    /**
     *
     *
     * @param string $sql
     * @return string
     */
    protected function replaceParameters($sql)
    {
        foreach( $this->parameters as $alias => $parameter ){
            if( is_string($alias) ){
                if( preg_match('/^\:[a-z0-9\-\_]+$/i', $alias) ){
                    $sql = str_replace($alias, $this->getQuoteStrategy()->quote($parameter), $sql);
                }
            }else{
                $sql = preg_replace('/\?{1}/', $this->getQuoteStrategy()->quote($parameter), $sql, 1);
            }
        }
        return $sql;
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
        $this->columns->setQuoteStrategy($quoteStrategy);
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
     * @return Query
     */
    public function setLimit($limit)
    {
        $this->limitSql = null;
        $this->limit = $limit;
        return $this;
    }

    /**
     * @param int $offset
     * @return Query
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
        if( is_array($groupBy) ){
            foreach ($groupBy as $group){
                if( $group ){
                    $this->groupByColumns[] = $group;
                }
            }
        }else{
            if( $groupBy ){
                $this->groupByColumns[] = $groupBy;
            }
        }
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
        if( $name ){
            $this->orderSql = null;
            $this->orderByColumns[] = array('column' => $name, 'type' => $type);
        }
        return $this;
    }

    /**
     *
     * into outfile
     * @param string $filename
     * @param string $terminated
     * @param string $enclosed
     * @param string $escaped
     * @param string $linesTerminated
     * @return Query
     */
    public function intoOutfile($filename, $terminated = ',', $enclosed = '"', $escaped = '\\\\', $linesTerminated ='\r\n')
    {
        $this->intoSql = null;
        $this->filename = $filename;
        $this->terminated = $terminated;
        $this->enclosed = $enclosed;
        $this->escaped = $escaped;
        $this->linesTerminated = $linesTerminated;
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

    /**
     *
     * @return string
     */
    public function getDefaultColumn(){
        return $this->columns->getDefaultColumn();
    }

    /**
     *
     * @param string $defaultColumn
     * @return Query
     */
    public function setDefaultColumn($defaultColumn)
    {
        $this->columns->setDefaultColumn((array) $defaultColumn);
        return $this;
    }

}