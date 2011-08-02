<?php

class AutoConditionalCriterion extends ConditionalCriterion
{

    /**
     * (non-PHPdoc)
     * @see Criterion::createSql()
     */
    public function createSql()
    {
    	$column = $this->column;
    	$value = $this->value;
    	$comparison = $this->comparison;
    	$mutatorColumn = $this->mutatorColumn;
    	$mutatorValue = $this->mutatorValue;

    	if( is_string($value) ){
    		$this->comparison = Criterion::JUST_LIKE;
    	}

    	if( is_numeric($value) ){
    		$this->comparison = Criterion::EQUAL;
    	}

    	if( is_array($value) ){
    		$this->comparison = Criterion::IN;
    	}

    	if( null === $value ){
    		$this->comparison = Criterion::IS_NULL;
    	}

    	if( Criterion::AUTO == $this->comparison ){
    		throw new Exception("No se encontro una comparacion automatica para este tipo de dato");
    	}

    	$sql = parent::createSql();

    	$this->column = $column;
    	$this->value = $value;
    	$this->comparision = $comparison;
    	$this->mutatorColumn = $mutatorColumn;
    	$this->mutatorValue = $mutatorValue;

        return $sql;
    }

}
