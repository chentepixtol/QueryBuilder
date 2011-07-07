<?php

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

    	$append = $prepend = '';
    	if( is_array($value) ){
    		$append = ')';
    		$prepend = '(';
    	}
        $value = $prepend.$this->quoteStrategy->quote($value).$append;
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
