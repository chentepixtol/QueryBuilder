<?php

namespace Query;

/**
 *
 * Mutator
 *
 * @package Query
 * @copyright (c) Vicente Mendoza <chentepixtol@gmail.com>
 * @author chentepixtol
 *
 */
class MutatorImpl implements Criterion
{

    /**
     *
     * @staticvar
     */
    const TYPE_COLUMN = 1;
    const TYPE_VALUE = 2;

    /**
     * @var mixed
     */
    private $value;

    /**
     * @var mixed
     */
    private $mutator;

    /**
     *
     * @var string
     */
    private $sql = null;

    /**
     *
     * @var int
     */
    private $type;

    /**
     *
     * @var QuoteStrategy
     */
    private $quoteStrategy;

    /**
     * @param unknown_type $value
     * @param unknown_type $mutator
     * @param QuoteStrategy $quoteStrategy
     * @param int $type
     */
    public function __construct($value, $mutator, $quoteStrategy, $type){
       $this->value = $value;
       $this->mutator = $mutator;
       $this->type = $type;
       $this->setQuoteStrategy($quoteStrategy);
    }

    /**
     * (non-PHPdoc)
     * @see Query.Criterion::createSql()
     */
    public function createSql()
    {
        if( null != $this->sql ){
            return $this->sql;
        }

        $value = $this->value;
        $mutator = $this->mutator;
        $type = $this->type;

        if( is_string($value) ){
            $value = str_replace('`', '', $value);
        }

        if( is_string($value) && preg_match('/^\:[a-z0-9\-\_]+$/i', $value) || $value == '?'){
            $value = new Expression($value);
        }

        if( Criterion::AS_FIELD == $mutator ){
            $mutator = null;
            $type = self::TYPE_COLUMN;
        }

        if( $mutator == Criterion::AS_EXPRESSION ){
            $value = new Expression($value);
            $mutator = null;
        }

        if( $type == self::TYPE_COLUMN ){
            $value = $this->quoteStrategy->quoteColumn($value);
        }else{
            $value = $this->quoteStrategy->quote($value);
        }

        if( $mutator ){
            $value = sprintf($mutator, $value);
        }

        $this->sql = $value;

        return $this->sql;
    }

    /**
     * @return MongoQuery
     */
    public function createMongoQuery(){

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
     * @param mixed $element
     * @return $boolean
     */
    public function contains($element){
        return false;
    }

}

