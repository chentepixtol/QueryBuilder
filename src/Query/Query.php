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
     * @var Columns
     */
    protected $columns;

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
     * @var Groups
     */
    protected $groupPart;

    /**
     *
     * @var Orders
     */
    protected $orderPart;

    /**
     *
     * @var Limits
     */
    protected $limitPart;

    /**
     *
     * @var Intos
     */
    protected $intoPart;

    /**
     *
     * @var array
     */
    protected $parameters = array();

    /**
     *
     * @var Joins
     */
    protected $joinPart;

    /**
     *
     * @var Froms
     */
    protected $fromPart;

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
        $this->joinPart = new Joins();
        $this->fromPart = new Froms();
        $this->groupPart = new Groups();
        $this->orderPart = new Orders();
        $this->limitPart = new Limits();
        $this->intoPart = new Intos();

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
        $this->joinPart = clone $this->joinPart;
        $this->fromPart = clone $this->fromPart;
        $this->groupPart = clone $this->groupPart;
        $this->orderPart = clone $this->orderPart;
        $this->limitPart = clone $this->limitPart;
        $this->intoPart = clone $this->intoPart;
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
     * @param strinf $type
     * @param string $alias
     * @return Criteria
     */
    public function joinOn($table, $type = Criterion::JOIN, $alias = null)
    {
        $on = $this->createCriteria();
        $on->setQuoteStrategy($this->quoteStrategy);
        $this->joinPart->join($table, $alias, $type, $on);
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
        $this->joinPart->join($table, $alias, $type, null, $usingColumn);
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
        $this->joinPart->reset();
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
        $this->joinPart->remove($table);
        return $this;
    }

    /**
     *
     * @param string $table
     * @return boolean
     */
    public function hasJoin($table){
        return $this->joinPart->contains($table);
    }

    /**
     *
     *
     * @param string $table
     * @return Query
     */
    public function from($table, $alias = null){
        $this->fromPart->addFrom($table, $alias);
        return $this;
    }

    /**
     *
     * @param unknown_type $table
     * @return boolean
     */
    public function hasFrom($table){
        return $this->fromPart->contains($table);
    }

    /**
     *
     * @param string $from
     * @return Query
     */
    public function removeFrom($from = null){
        $this->fromPart->remove($from);
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
    public function createFromSql(){
        return $this->fromPart->createSql();
    }

    /* (non-PHPdoc)
     * @see SelectCriterion::createJoinSql()
     */
    public function createJoinSql(){
        return $this->joinPart->createSql();
    }

    /* (non-PHPdoc)
     * @see SelectCriterion::createGroupSql()
     */
    public function createGroupSql(){
        return $this->groupPart->createSql();
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
    public function createOrderSql(){
        return $this->orderPart->createSql();
    }

    /* (non-PHPdoc)
     * @see SelectCriterion::createLimitSql()
     */
    public function createLimitSql(){
        return $this->limitPart->createSql();
    }

    /**
     * (non-PHPdoc)
     * @see Query.SelectCriterion::createIntoSql()
     */
    public function createIntoSql(){
       return $this->intoPart->createSql();
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
     * TODO Mejorar metodo
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
        $this->joinPart->setQuoteStrategy($quoteStrategy);
        $this->fromPart->setQuoteStrategy($quoteStrategy);
        $this->groupPart->setQuoteStrategy($quoteStrategy);
        $this->orderPart->setQuoteStrategy($quoteStrategy);
        $this->limitPart->setQuoteStrategy($quoteStrategy);
        $this->intoPart->setQuoteStrategy($quoteStrategy);
        return $this;
    }

    /**
     * @return the $quoteStrategy
     */
    public function getQuoteStrategy() {
        return $this->quoteStrategy;
    }

    /**
     *
     * @param int $page
     * @param int $itemsPerPage
     * @return Query
     */
    public function page($page, $itemsPerPage){
        $this->limitPart->page($page, $itemsPerPage);
        return $this;
    }

    /**
     * @return int
     */
    public function getLimit() {
        return $this->limitPart->getLimit();
    }

    /**
     * @return int
     */
    public function getOffset() {
        return $this->limitPart->getOffset();
    }

    /**
     * @param int $limit
     * @return Query
     */
    public function setLimit($limit)
    {
        $this->limitPart->setLimit($limit);
        return $this;
    }

    /**
     * @param int $offset
     * @return Query
     */
    public function setOffset($offset)
    {
        $this->limitPart->setOffset($offset);
        return $this;
    }

    /**
     * GUarda una columna para ordenar los resultados
     * @param string $groupBy
     * @return Query
     */
    public function addGroupBy($groupBy)
    {
        $this->groupPart->addGroupBy($groupBy);
        return $this;
    }

    /**
     * @return array
     */
    public function getGroupByColumns() {
        return $this->groupPart->getGroups();
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
        $this->orderPart->orderBy($name, $type);
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
        $this->intoPart->intoOutfile($filename, $terminated, $enclosed, $escaped, $linesTerminated);
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