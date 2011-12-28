<?php

namespace Query;

/**
 *
 * ConditionalCriterion
 *
 * @package Query
 * @copyright (c) Vicente Mendoza <chentepixtol@gmail.com>
 * @author chentepixtol
 *
 */
class ConditionalCriterion implements Criterion
{

    /**
     *
     *
     * @var string
     */
    protected $column;

    /**
     *
     *
     * @var string
     */
    protected $comparison;

    /**
     *
     *
     * @var mixed
     */
    protected $value;

    /**
     *
     *
     * @var string
     */
    protected $mutatorColumn = null;

    /**
     *
     *
     * @var string
     */
    protected $mutatorValue = null;

    /**
     *
     *
     * @var QuoteStrategy
     */
    protected $quoteStrategy;

    /**
     *
     * @var string
     */
    protected $sql;

    /**
     *
     *
     * @staticvar
     */
    protected static $BinaryComparison = array(Criterion::IS_NULL, Criterion::IS_NOT_NULL);
    protected static $Likes = array(
        Criterion::JUST_LIKE, Criterion::LIKE, Criterion::LEFT_LIKE,
        Criterion::RIGHT_LIKE, Criterion::NOT_LIKE, Criterion::NOT_JUST_LIKE
    );

    /**
     *
     *
     * @param string $column
     * @param string $value
     * @param string $comparison
     * @param string $mutatorColumn
     * @param string $mutatorValue
     */
    public function __construct($column, $value, $comparison = Criterion::EQUAL,
    $mutatorColumn = null, $mutatorValue = null)
    {
        $this->column = $column;
        $this->comparison = $comparison;
        $this->value = $value;
        $this->mutatorColumn = $mutatorColumn;
        $this->mutatorValue = $mutatorValue;
    }

    /**
     * @param string $column
     * @param string $value
     * @param string $comparison
     * @param string $mutatorColumn
     * @param string $mutatorValue
     * @return ConditionalCriterion
     */
    public static function factory($column, $value, $comparison = Criterion::EQUAL,
    $mutatorColumn = null, $mutatorValue = null)
    {
        if( Criterion::AUTO == $comparison ){
            $criterion = new AutoConditionalCriterion($column, $value, $comparison, $mutatorColumn, $mutatorValue);
        }
        else{
            $criterion = new ConditionalCriterion($column, $value, $comparison, $mutatorColumn, $mutatorValue);
        }
        return $criterion;
    }

    /**
     * (non-PHPdoc)
     * @see Query.Criterion::contains()
     */
    public function contains($element){
        return $this->column == $element;
    }

    /**
     * (non-PHPdoc)
     * @see Criterion::createSql()
     */
    public function createSql()
    {
        if( null !== $this->sql )
            return $this->sql;

        $column = is_string($this->column) ? str_replace('`', '', $this->column) : $this->column;
        $mutatorValue = $this->mutatorValue;
        $mutatorColumn = $this->mutatorColumn;
        $value = $this->value;
        $comparision = $this->comparison;

        if( $mutatorValue == Criterion::AS_EXPRESSION ){
            $value = new Expression($value);
            $mutatorValue = null;
        }

        if( $mutatorColumn == Criterion::AS_EXPRESSION ){
            $column = new Expression($column);
            $mutatorColumn = null;
        }

        $column = $this->quoteStrategy->quoteColumn($column);
        if( is_string($value) && preg_match('/^\:[a-z0-9\-\_]+$/i', $value) || $value == '?'){
            $value = new Expression($value);
        }

        if( in_array($this->comparison, self::$Likes) ){
            $aux = str_replace(' ','%', $comparision);
            $comparision = str_replace('_',' ', $comparision);
            $aux = str_replace('_',' ', $aux);
            $value = str_replace(array('NOT LIKE','LIKE'), $value, $aux);
        }

        if( $this->comparison == Criterion::RANGE ){
            $comparision = Criterion::IN;
            $range = new Range();
            $range->fromString($value);
            $value = $range->toArray();
        }

        $part1 = $mutatorColumn ? sprintf($mutatorColumn, $column) : $column;

        $append = $prepend = '';
        if( is_array($value) || $comparision == Criterion::IN ){
            $append = ')';
            $prepend = '(';
        }
        if( Criterion::AS_FIELD == $mutatorValue ){
            $value = $this->quoteStrategy->quoteColumn($value);
            $mutatorValue = '%s';
        }
        else{
            $value = $prepend.$this->quoteStrategy->quote($value).$append;
        }

        $part3 = $mutatorValue ? sprintf($mutatorValue, $value) : $value;

        if( Criterion::BETWEEN == $comparision ){
            $part3 = str_replace(array('(',')', ','), array('','',' AND'), $part3);
        }

        if( in_array($comparision, self::$BinaryComparison) ){
            $part3 = '';
        }

        $part1 = trim($part1);
        $part2 = trim($comparision);
        $part3 = trim($part3);
        $this->sql = trim("{$part1} {$part2} {$part3}");
        return $this->sql;
    }

    /**
     * (non-PHPdoc)
     * @see Criterion::setQuoteStrategy()
     */
    public function setQuoteStrategy(QuoteStrategy $quoteStrategy){
        $this->quoteStrategy = $quoteStrategy;
    }

}
