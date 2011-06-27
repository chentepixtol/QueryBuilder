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
     * @staticvar
     */
    private static $BinaryComparison = array(Criterion::IS_NULL, Criterion::IS_NOT_NULL);
    private static $Likes = array(
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
     * (non-PHPdoc)
     * @see Criterion::createSql()
     */
    public function createSql()
    {
    	$column =  $this->quoteStrategy->quoteColumn(str_replace('`', '', $this->column));
    	$mutatorValue = $this->mutatorValue;
    	$value = $this->value;
    	$comparision = $this->comparison;

    	if( in_array($this->comparison, self::$Likes) ){
    		$aux = str_replace(' ','%', $comparision);
    		$comparision = str_replace('_',' ', $comparision);
    		$aux = str_replace('_',' ', $aux);
    		$value = str_replace(array('NOT LIKE','LIKE'), $value, $aux);
        }

        $part1 = $this->mutatorColumn ? sprintf($this->mutatorColumn, $column) : $column;

        $value = $this->quoteStrategy->quote($value);
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
		$sql = trim("{$part1} {$part2} {$part3}");
        return $sql;
    }

    /**
     * (non-PHPdoc)
     * @see Criterion::setQuoteStrategy()
     */
    public function setQuoteStrategy(QuoteStrategy $quoteStrategy){
        $this->quoteStrategy = $quoteStrategy;
    }

}
