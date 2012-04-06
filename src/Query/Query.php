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
class Query extends ManipulationStatement implements SelectCriterion
{

    /**
     *
     * @var string
     */
    const ALL_COLUMNS = '*';
    const ASC = 'ASC';
    const DESC = 'DESC';

    /**
     * @var Columns
     */
    protected $columns;

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
     * @var Intos
     */
    protected $intoPart;

    /**
     *
     * @var Criteria
     */
    protected $havingCriteria;

    /**
     *
     * Construct
     * @param QuoteStrategy $quoteStrategy
     */
    public function __construct(QuoteStrategy $quoteStrategy = null)
    {
        $this->columns = new Columns();
        $this->groupPart = new Groups();
        $this->orderPart = new Orders();
        $this->intoPart = new Intos();
        $this->havingCriteria = $this->createCriteria();
        parent::__construct($quoteStrategy);
    }

    /**
     *
     * Cloning
     */
    public function __clone()
    {
        $this->columns = clone $this->columns;
        $this->groupPart = clone $this->groupPart;
        $this->orderPart = clone $this->orderPart;
        $this->intoPart = clone $this->intoPart;
        $this->havingCriteria = clone $this->havingCriteria;
        $this->havingCriteria->setQuery($this);
        parent::__clone();
    }

    /**
     *
     * @param string $column
     * @return self
     */
    public function removeColumn($column = null)
    {
        $this->columns->removeColumn($column);
        return $this;
    }

    /**
     *
     * @param boolean $flag
     * @return self
     */
    public function distinct($flag = true){
        $this->columns->distinct($flag);
        return $this;
    }

    /**
     *
     * @param mixed $column
     * @return self
     */
    public function select(){
        $this->columns->addColumns(func_get_args());
        return $this;
    }

    /**
     *
     * addColumns
     * @param array $columns
     * @return self
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
     * @return self
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
    public function having(){
        return $this->havingCriteria;
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

    /* (non-PHPdoc)
     * @see SelectCriterion::createSelectSql()
     */
    public function createSelectSql()
    {
        return 'SELECT'.$this->columns->createSql();
    }

    /* (non-PHPdoc)
     * @see SelectCriterion::createGroupSql()
     */
    public function createGroupSql(){
        return $this->groupPart->createSql();
    }

    /* (non-PHPdoc)
     * @see SelectCriterion::createOrderSql()
     */
    public function createOrderSql(){
        return $this->orderPart->createSql();
    }

    /**
     * (non-PHPdoc)
     * @see Query.SelectCriterion::createIntoSql()
     */
    public function createIntoSql(){
       return $this->intoPart->createSql();
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
     * @return MongoQuery
     */
    public function createMongoQuery(){

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

    /**
     * (non-PHPdoc)
     * @see Criterion::setQuoteStrategy()
     * @return self
     */
    public function setQuoteStrategy(QuoteStrategy $quoteStrategy)
    {
        parent::setQuoteStrategy($quoteStrategy);
        $this->havingCriteria->setQuoteStrategy($quoteStrategy);
        $this->columns->setQuoteStrategy($quoteStrategy);
        $this->groupPart->setQuoteStrategy($quoteStrategy);
        $this->orderPart->setQuoteStrategy($quoteStrategy);
        $this->intoPart->setQuoteStrategy($quoteStrategy);
        return $this;
    }

    /**
     * GUarda una columna para ordenar los resultados
     * @param string $groupBy
     * @return self
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
     * @param string $group
     */
    public function hasGroup($group){
        return $this->groupPart->contains($group);
    }

    /**
     *
     * order by
     * @param string $name
     * @param string $type
     * @return self
     */
    public function orderBy($name, $type = Query::ASC)
    {
        $this->orderPart->orderBy($name, $type);
        return $this;
    }

    /**
     *
     * @param string $group
     */
    public function hasOrder($column){
        return $this->orderPart->contains($column);
    }

    /**
     *
     * into outfile
     * @param string $filename
     * @param string $terminated
     * @param string $enclosed
     * @param string $escaped
     * @param string $linesTerminated
     * @return self
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
     * @return self
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
     * @return self
     */
    public function setDefaultColumn($defaultColumn)
    {
        $this->columns->setDefaultColumn((array) $defaultColumn);
        return $this;
    }

}