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
abstract class ManipulationStatement implements Criterion
{

    /**
     *
     * @var QuoteStrategy
     */
    protected $quoteStrategy;

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
     * @var Limits
     */
    protected $limitPart;

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
        $this->joinPart = new Joins();
        $this->fromPart = new Froms();
        $this->limitPart = new Limits();

        $this->setQuoteStrategy($quoteStrategy ? $quoteStrategy : new SimpleQuoteStrategy());
        $this->init();
    }

    /**
     * Initialization
     */
    protected function init(){
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
        $this->joinPart = clone $this->joinPart;
        $this->fromPart = clone $this->fromPart;
        $this->limitPart = clone $this->limitPart;
    }

    /**
     *
     * Factory
     * @param QuoteStrategy $quoteStrategy
     * @return self
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
     * @return self
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
     * @return self
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
     * @return self
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
     * @return self
     */
    public function rightJoinUsing($table, $usingColumn, $alias = null){
        return $this->joinUsing($table, $usingColumn, Criterion::RIGHT_JOIN, $alias);
    }

    /**
     *
     * @return self
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
     * @return self
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
     * @return self
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
     * @return self
     */
    public function removeFrom($from = null){
        $this->fromPart->remove($from);
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
     * @return self
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

    /** (non-PHPdoc)
     * @see SelectCriterion::createFromSql()
     */
    public function createFromSql(){
        return $this->fromPart->createSql();
    }

    /** (non-PHPdoc)
     * @see SelectCriterion::createJoinSql()
     */
    public function createJoinSql(){
        return $this->joinPart->createSql();
    }

    /** (non-PHPdoc)
     * @see SelectCriterion::createHavingSql()
     */
    public function createHavingSql()
    {
        if( $this->havingCriteria->isEmpty() ){
            return '';
        }
        return "HAVING ".$this->havingCriteria->createSql();
    }

    /** (non-PHPdoc)
     * @see SelectCriterion::createLimitSql()
     */
    public function createLimitSql(){
        return $this->limitPart->createSql();
    }

    /**
     *
     * Bind the values to the query
     * @param array $parameters
     * @return self
     */
    public function bind($parameters){
        $this->parameters = $parameters;
        return $this;
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
     * (non-PHPdoc)
     * @see Criterion::setQuoteStrategy()
     */
    public function setQuoteStrategy(QuoteStrategy $quoteStrategy)
    {
        $this->quoteStrategy = $quoteStrategy;
        $this->whereCriteria->setQuoteStrategy($quoteStrategy);
        $this->havingCriteria->setQuoteStrategy($quoteStrategy);
        $this->joinPart->setQuoteStrategy($quoteStrategy);
        $this->fromPart->setQuoteStrategy($quoteStrategy);
        $this->limitPart->setQuoteStrategy($quoteStrategy);
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
     * @return self
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
     * @return self
     */
    public function setLimit($limit)
    {
        $this->limitPart->setLimit($limit);
        return $this;
    }

    /**
     * @param int $offset
     * @return self
     */
    public function setOffset($offset)
    {
        $this->limitPart->setOffset($offset);
        return $this;
    }
}