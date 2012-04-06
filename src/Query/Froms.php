<?php

namespace Query;

/**
 *
 * Join
 *
 * @package Query
 * @copyright (c) Vicente Mendoza <chentepixtol@gmail.com>
 * @author chentepixtol
 *
 */
class Froms implements Criterion
{

    /**
     *
     * Lazy Load
     * @var string
     */
    protected $sql;

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
     *
     * @var string
     */
    protected $keyword = "FROM ";

    /**
     * (non-PHPdoc)
     * @see Query.Criterion::createSql()
     */
    public function createSql()
    {
        if( null !== $this->sql ){
            return $this->sql;
        }

        $tables = count($this->from);

        if( 0 === $tables){
            throw new Exception("No se ha definido ninguna tabla en la parte sql del FROM");
        }

        $sql = $this->keyword;
        $number = 1;
        foreach( $this->from as $alias => $table ){
            $alias = is_string($alias) ? ' as '.$this->quoteStrategy->quoteTable($alias) : '';
            $sql .= $this->quoteStrategy->quoteTable($table).$alias;
            if( $tables != $number ) $sql.= ', ';
            $number++;
        }
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
     *
     * @param string $table
     * @param string $alias
     */
    public function addFrom($table, $alias = null)
    {
        $this->sql = null;
        if( is_string($alias) ){
            $this->from[$alias] = $table;
        }else{
            $this->from[] = $table;
        }
    }

    /**
     *
     * @param string $from
     */
    public function remove($from = null)
    {
        $this->sql = null;
        if( $from ){
            $alias = array_search($from, $this->from);
            if( $alias !== false ) unset($this->from[$alias]);
        }
        else {
            $this->from = array();
        }
    }

    /**
     *
     * @param string $keyword
     */
    public function setKeyword($keyword){
        $this->sql = null;
        $this->keyword = $keyword;
    }

    /**
     * (non-PHPdoc)
     * @see Query.Criterion::contains()
     */
    public function contains($table){
        return isset($this->from[$table]) || in_array($table, $this->from);
    }

    /**
     *
     * @param QuoteStrategy $quoteStrategy
     */
    public function setQuoteStrategy(QuoteStrategy $quoteStrategy){
        $this->quoteStrategy = $quoteStrategy;
    }

}

