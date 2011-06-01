<?php

class ConditionalCriterion implements Criterion
{

	/**
	 *
	 *
	 * @var string
	 */
    private $column;

    /**
	 *
	 *
	 * @var string
	 */
    private $comparison;

    /**
	 *
	 *
	 * @var mixed
	 */
    private $value;

    /**
	 *
	 *
	 * @var string
	 */
    private $mutatorColumn = null;

    /**
	 *
	 *
	 * @var string
	 */
    private $mutatorValue = null;

    /**
	 *
	 *
	 * @var QuoteStrategy
	 */
    private $quoteStrategy;

    /**
     *
     *
     * @var array
     */
    private static $BinaryComparison = array(Criterion::IS_NULL, Criterion::IS_NOT_NULL);

    /**
     *
     *
     * @param string $column
     * @param string $value
     * @param string $comparison
     * @param string $mutatorColumn
     * @param string $mutatorValue
     */
    public function __construct($column, $value, $comparison = Criterion::EQUAL, $mutatorColumn = null, $mutatorValue = null){
        $this->column = $column;
        $this->comparison = $comparison;
        $this->value = $value;
        $this->mutatorColumn = $mutatorColumn;
        $this->mutatorValue = $mutatorValue;
    }

    /**
     * (non-PHPdoc)
     * @see Criterion::createSql()
     */
    public function createSql()
    {
        $part1 = $this->mutatorColumn ? sprintf($this->mutatorColumn, $this->quoteStrategy->quoteColumn($this->column))
        	: $this->quoteStrategy->quoteColumn($this->column);

        $value = $this->quoteStrategy->quote($this->value);
        if( is_array($value) && Criterion::IN == $this->comparison)
            $value = '('. implode(', ', $value) . ')';

        $part3 = $this->mutatorValue ? sprintf($this->mutatorValue, $value) : $value;

        if( in_array($this->comparison, self::$BinaryComparison) )
            $part3 = '';

        return " {$part1} {$this->comparison} {$part3} ";
    }

    /**
     * (non-PHPdoc)
     * @see Criterion::setQuoteStrategy()
     */
    public function setQuoteStrategy(QuoteStrategy $quoteStrategy){
        $this->quoteStrategy = $quoteStrategy;
    }

}
