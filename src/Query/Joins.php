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
class Joins implements Criterion
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
    protected $joins = array();

    /**
     * (non-PHPdoc)
     * @see Query.Criterion::createSql()
     */
    public function createSql()
    {
        if( null !== $this->sql ){
            return $this->sql;
        }

        $sql = '';
        foreach( $this->joins as $join ){
            $sql .=  $join['type'].' '.  $this->quoteStrategy->quoteTable($join['table']);
            if( $join['alias'] && is_string($join['alias'])  ){
                $sql.=' as '. $this->quoteStrategy->quoteTable($join['alias']);
            }
            if( $join['using'] ){
                $field = $this->quoteStrategy->quoteColumn($join['using']);
                $sql .= " USING( {$field} ) ";
            }
            else{
                if( $join['on'] instanceof Criterion ){
                    $sql .= " ON{$join['on']->createSql()} ";
                }
            }

        }
        $this->sql = trim($sql);
        return $this->sql;
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
    public function join($table, $alias, $type, $on = null, $using = null)
    {
        $this->sql = null;
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
     */
    public function reset()
    {
        $this->sql = null;
        $this->joins = array();
    }

    /**
     *
     *
     * @param string $table
     */
    public function remove($table)
    {
        $this->sql = null;
        if( isset($this->joins[$table]) ){
            unset($this->joins[$table]);
        }
    }

    /**
     * (non-PHPdoc)
     * @see Query.Criterion::contains()
     */
    public function contains($table){
        return isset($this->joins[$table]);
    }

    /**
     *
     * @param QuoteStrategy $quoteStrategy
     */
    public function setQuoteStrategy(QuoteStrategy $quoteStrategy){
        $this->quoteStrategy = $quoteStrategy;
    }
}

