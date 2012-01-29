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
class Orders implements Criterion
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
     * Order Column
     * @var mixed
     */
    protected $orderByColumns = array ();

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
        if ( count ($this->orderByColumns ) ) {
            $this->sql = "ORDER BY ";
            $columns = array_map(array($this, '_quoteOrder'), $this->orderByColumns);
            $this->sql .= implode(', ',  $columns);
        }

        return $this->sql;
    }

    /**
     *
     * order by
     * @param string $name
     * @param string $type
     */
    public function orderBy($name, $type = Query::ASC)
    {
        if( $name ){
            $this->sql = null;
            $this->orderByColumns[] = array('column' => $name, 'type' => $type);
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

    /**
     *
     *
     * @param array $orderArray
     * @return string
     */
    protected function _quoteOrder($orderArray){
        return $this->quoteStrategy->quoteColumn($orderArray['column']) .' '. $orderArray['type'];
    }

}

