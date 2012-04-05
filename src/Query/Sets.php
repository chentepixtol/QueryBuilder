<?php

namespace Query;

/**
 *
 * Sets
 *
 * @package Query
 * @copyright (c) Vicente Mendoza <chentepixtol@gmail.com>
 * @author chentepixtol
 *
 */
class Sets implements Criterion
{

    /**
     *
     * Lazy Load
     * @var string
     */
    protected $sql = null;

    /**
     *
     * @var QuoteStrategy
     */
    protected $quoteStrategy;

    /**
     *
     * @var array
     */
    protected $sets = array();

    /**
     * (non-PHPdoc)
     * @see Query.SelectCriterion::createIntoSql()
     */
    public function createSql()
    {
        if( null !== $this->sql ){
            return $this->sql;
        }

        $size = count($this->sets);
        if( $size == 0 ){
            throw new Exception("No se han definido campos a actualizar");
        }

        $this->sql = 'SET ';
        $i = 0;
        foreach ($this->sets as $column => $set){
            $i++;

            $columnPart = $this->quoteStrategy->quoteColumn($column);
            $mutator = new MutatorImpl($set['value'], $set['mutatorValue'], $this->quoteStrategy, MutatorImpl::TYPE_VALUE);
            $valuePart = $mutator->createSql();
            $this->sql .= "{$columnPart} = {$valuePart}";
            if( $size != $i ){
                $this->sql .= ", ";
            }
        }

        return $this->sql;
    }

    /**
     *
     * @param string $column
     * @param mixed $value
     */
    public function add($column, $value, $mutatorValue = null){
        $this->sql = null;
        $this->sets[$column] = array(
            'value' => $value,
            'column' => $column,
            'mutatorValue' => $mutatorValue,
        );
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

