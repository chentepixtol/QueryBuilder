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
class Intos implements Criterion
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
     * (non-PHPdoc)
     * @see Query.SelectCriterion::createIntoSql()
     */
    public function createSql()
    {
        if( null !== $this->sql ){
            return $this->sql;
        }
        $this->sql = '';
        if( null != $this->filename ){
            $this->sql = "INTO OUTFILE '{$this->filename}'";
            if( $this->terminated ){
                $this->sql .= " FIELDS TERMINATED BY '{$this->terminated}'";
            }
            if( $this->enclosed ){
                $this->sql .= " ENCLOSED BY '{$this->enclosed}'";
            }
            if( $this->escaped ){
                $this->sql .= " ESCAPED BY '{$this->escaped}'";
            }
            if( $this->linesTerminated ){
                $this->sql .= " LINES TERMINATED BY '{$this->linesTerminated}'";
            }
        }

        return $this->sql;
    }

    /**
     * @return MongoQuery
     */
    public function createMongoQuery(){

    }

    /**
     *
     * into outfile
     * @param string $filename
     * @param string $terminated
     * @param string $enclosed
     * @param string $escaped
     * @param string $linesTerminated
     */
    public function intoOutfile($filename, $terminated = ',', $enclosed = '"', $escaped = '\\\\', $linesTerminated ='\r\n')
    {
        $this->sql = null;
        $this->filename = $filename;
        $this->terminated = $terminated;
        $this->enclosed = $enclosed;
        $this->escaped = $escaped;
        $this->linesTerminated = $linesTerminated;
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

