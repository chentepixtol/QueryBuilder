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
class Limits implements Criterion
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
     * Limit
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
     * (non-PHPdoc)
     * @see Query.Criterion::createSql()
     */
    public function createSql()
    {
        if( null !== $this->sql ){
            return $this->sql;
        }

        $this->sql = '';
        if ($this->limit != 0) {
            $this->sql .= "LIMIT " . $this->limit;
        }
        if ($this->offset != 0) {
            $this->sql .= " OFFSET " . $this->offset;
        }

        return $this->sql;
    }

    /**
     *
     * @param int $page
     * @param int $itemsPerPage
     */
    public function page($page, $itemsPerPage){
        $this->setLimit($itemsPerPage);
        $this->setOffset( ($page-1) * $itemsPerPage );
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
        $this->sql = null;
        $this->limit = $limit;
    }

    /**
     * @param int $offset
     */
    public function setOffset($offset)
    {
        $this->sql = null;
        $this->offset = $offset;
    }

    /**
     * (non-PHPdoc)
     * @see Query.Criterion::contains()
     */
    public function contains($table){
        return false;
    }

    /**
     *
     * @param QuoteStrategy $quoteStrategy
     */
    public function setQuoteStrategy(QuoteStrategy $quoteStrategy){
        $this->quoteStrategy = $quoteStrategy;
    }
}

