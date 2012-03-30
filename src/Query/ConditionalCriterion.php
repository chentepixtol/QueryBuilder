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
     *
     * @param mixed $column
     * @param mixed $mutatorColumn
     * @return string
     */
    private function calculatePart1($column, $mutatorColumn)
    {
        if( is_string($column) ){
            $column = str_replace('`', '', $column);
        }

        if( $mutatorColumn == Criterion::AS_EXPRESSION ){
            $column = new Expression($column);
            $mutatorColumn = null;
        }

        $column = $this->quoteStrategy->quoteColumn($column);

        $part1 = $mutatorColumn ? sprintf($mutatorColumn, $column) : $column;

        return trim($part1);
    }

    /**
     *
     * @param string $comparision
     * @return string
     */
    private function calculatePart2($comparision)
    {
        if( in_array($comparision, self::$Likes) ){
            $comparision = str_replace('_',' ', $comparision);
        }

        if( $comparision == Criterion::RANGE ){
            $comparision = Criterion::IN;
        }

        return trim($comparision);
    }

    /**
     *
     * @param mixed $value
     * @param mixed $mutatorValue
     * @param mixed $comparision
     * @return string
     */
    private function calculatePart3($value, $mutatorValue, $comparision)
    {
        if( $mutatorValue == Criterion::AS_EXPRESSION ){
            $value = new Expression($value);
            $mutatorValue = null;
        }

        if( is_string($value) && preg_match('/^\:[a-z0-9\-\_]+$/i', $value) || $value == '?'){
            $value = new Expression($value);
        }

        if( in_array($comparision, self::$Likes) ){
            $aux = str_replace(' ','%', $comparision);
            $aux = str_replace('_',' ', $aux);
            $value = str_replace(array('NOT LIKE','LIKE'), $value, $aux);
        }

        if( $comparision == Criterion::RANGE ){
            $comparision = Criterion::IN;
            $range = new Range();
            $range->fromString($value);
            $value = $range->toArray();
        }

        $append = $prepend = null;
        if( is_array($value) || $comparision == Criterion::IN ){
            $append = ')';
            $prepend = '(';
            if( empty($value) ){
                $value = array(-1);
            }
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

        return trim($part3);
    }



    /**
     * (non-PHPdoc)
     * @see Criterion::createSql()
     */
    public function createSql()
    {
        if( null !== $this->sql ){
            return $this->sql;
        }

        $part1 = $this->calculatePart1($this->column, $this->mutatorColumn);
        $part2 = $this->calculatePart2($this->comparison);
        $part3 = $this->calculatePart3($this->value, $this->mutatorValue, $this->comparison);
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
