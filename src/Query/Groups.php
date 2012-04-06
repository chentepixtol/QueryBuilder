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
class Groups implements Criterion
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
     * Columnas por las que se agruparan los resultados
     * @var mixed
     */
    protected $groupByColumns = array ();

    /**
     * (non-PHPdoc)
     * @see Query.Criterion::createSql()
     */
    public function createSql()
    {
        if( null !== $this->sql ){
            return $this->sql;
        }

        $this->sql = '';
        if (count ( $this->groupByColumns )) {
            $this->sql .= "GROUP BY ";
            $columns = array_map(array($this->quoteStrategy, 'quoteColumn'), $this->groupByColumns);
            $this->sql .= implode(', ', $columns);
        }

        return $this->sql;
    }

    /**
     * @return MongoQuery
     */
    public function createMongoQuery(){

    }

    /**
     * GUarda una columna para ordenar los resultados
     * @param string $groupBy
     */
    public function addGroupBy($groupBy)
    {
        $this->sql = null;
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
    }

    /**
     *
     * @return array
     */
    public function getGroups(){
        return $this->groupByColumns;
    }

    /**
     * (non-PHPdoc)
     * @see Query.Criterion::contains()
     */
    public function contains($group){
        return in_array($group, $this->groupByColumns);
    }

    /**
     *
     * @param QuoteStrategy $quoteStrategy
     */
    public function setQuoteStrategy(QuoteStrategy $quoteStrategy){
        $this->quoteStrategy = $quoteStrategy;
    }

}

