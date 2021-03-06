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
     * Mutators
     * @var array
     */
    protected $mutators = array();

    /**
     *
     * @var QuoteStrategy
     */
    protected $quoteStrategy;

    /**
     *
     * @var string
     */
    protected $defaultColumn = array('*');

    /**
     *
     * @var boolean
     */
    protected $distinct = false;

    /**
     *
     * @param string $column
     * @return Columns
     */
    public function removeColumn($column = null)
    {
        $this->sql = null;
        if( $column ){
            $alias = array_search($column, $this->columns);
            if( $alias !== false ){
                unset($this->mutators[$alias]);
                unset($this->columns[$alias]);
            }
        }
        else {
            $this->columns = array();
        }
        return $this;
    }

    /**
     *
     *
     * @param boolean $flag
     * @return Columns
     */
    public function distinct($flag = true){
        $this->sql = null;
        $this->distinct = $flag;
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
     * @param string $mutator
     * @return Columns
     */
    public function addColumn($column, $alias = null, $mutator = null)
    {
        if( $column ){
            $this->sql = null;
            if( is_string($alias) ){
                $this->mutators[$alias] = $mutator;
                $this->columns[$alias] = $column;
            }else{
                $this->columns[] = $column;
                $this->mutators[] = $mutator;
            }
        }
        return $this;
    }

    /**
     * (non-PHPdoc)
     * @see Query.Criterion::contains()
     */
    public function contains($element){
        return in_array($element, $this->columns);
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

        if( $this->distinct ){
            $sql.= ' DISTINCT';
        }

        if( empty($this->columns) ){
            $sql .= $this->createSqlForColumns($this->getDefaultColumn());
        }

        $sql .= $this->createSqlForColumns($this->columns);

        $this->sql = $sql;
        return $this->sql;
    }

    /**
     * @return MongoQuery
     */
    public function createMongoQuery(){

    }

    /**
     *
     * @return string
     */
    protected function createSqlForColumns(array $columns)
    {
        $sql = '';
        $size = count($columns);
        $number = 0;
        foreach ($columns as $alias => $column)
        {
            $mutator = isset($this->mutators[$alias]) ? $this->mutators[$alias] : null;
            $sql .= ' '. $this->createSqlForColumn($column, $alias, $mutator);
            $number++;
            if( $number != $size ) $sql.= ',';
        }
        return $sql;
    }

    /**
     *
     * @param string $column
     * @param string $alias
     * @param string $mutator
     * @return string
     */
    protected function createSqlForColumn($column, $alias = null, $mutator = null)
    {
        $mutatorColumn = new MutatorImpl($column, $mutator, $this->quoteStrategy, MutatorImpl::TYPE_COLUMN);

        $sql = $mutatorColumn->createSql();
        if( is_string($alias) ){
            $sql.= ' as '. $this->quoteStrategy->quoteColumn($alias);
        }
        return $sql;
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
     * @param array $defaultColumn
     */
    public function setDefaultColumn($defaultColumn)
    {
        $this->sql = null;
        $this->defaultColumn = $defaultColumn;
    }

}

