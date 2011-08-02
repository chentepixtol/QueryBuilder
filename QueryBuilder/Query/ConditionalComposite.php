<?php

/**
 *
 * ConditionalComposite
 *
 * @package Query
 * @copyright (c) Vicente Mendoza <chentepixtol@gmail.com>
 * @author chentepixtol
 *
 */
class ConditionalComposite implements CriterionComposite
{

	/**
	 *
	 * @var array
	 */
    protected $criterions = array();

    /**
     *
     *
     * @var string
     */
    protected $operatorLogic;

    /**
     *
     * @var CriterionComposite
     */
    protected $parent = null;

    /**
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
     * @param string $operatorLogic
     */
    public function __construct($operatorLogic = CriterionComposite::LOGICAL_AND){
        $this->operatorLogic = $operatorLogic;
    }

    /**
     * (non-PHPdoc)
     * @see CriterionComposite::addCriterion()
     */
    public function addCriterion(Criterion $criterion)
    {
    	$this->sql = null;
        $this->criterions[] = $criterion;
        $criterion->setQuoteStrategy($this->quoteStrategy);
        if( $criterion instanceof CriterionComposite )
        	$criterion->setParent($this);
        return $this;
    }

    /**
     * (non-PHPdoc)
     * @see Criterion::createSql()
     */
   public function createSql()
   {
   		if( null !== $this->sql )
   			return $this->sql;

   		if( $this->isEmpty() ){
   			$this->sql = '( 1 )';
			return $this->sql;
		}

        $sql = '';
        $total = $this->count();
        foreach( $this->getChildrens() as $i => $criterion ){
            $sql .= $criterion->createSql();
            if( $total != $i + 1 ) $sql .= ' '. $this->getOperatorLogic() . ' ';
        }
        $this->sql = '( ' . $sql . ' )';
        return $this->sql;
    }

    /**
     * (non-PHPdoc)
     * @see Criterion::setQuoteStrategy()
     */
    public function setQuoteStrategy(QuoteStrategy $quoteStrategy){
        $this->quoteStrategy = $quoteStrategy;
    }

	/**
	 * (non-PHPdoc)
	 * @see CriterionComposite::getParent()
	 */
	public function getParent() {
		return $this->parent instanceof CriterionComposite ? $this->parent : $this;
	}

	/**
	 * (non-PHPdoc)
	 * @see CriterionComposite::setParent()
	 */
	public function setParent(CriterionComposite $parent) {
		$this->parent = $parent;
		return $this;
	}

	/**
	 * (non-PHPdoc)
	 * @see CriterionComposite::getChildrens()
	 */
	public function getChildrens(){
		return $this->criterions;
	}

	/**
	 * (non-PHPdoc)
	 * @see CriterionComposite::count()
	 */
	public function count(){
		return count($this->getChildrens());
	}

	/**
	 * (non-PHPdoc)
	 * @see CriterionComposite::isEmpty()
	 */
	public function isEmpty(){
		return $this->count() == 0;
	}

	/**
     *
     * @return string
     */
    public function getOperatorLogic(){
        return $this->operatorLogic;
    }

    /**
     *
     *
     * @param string $opertatorLogic
     * @return CriterionComposite
     */
    public function setOperatorLogic($opertatorLogic){
    	$this->operatorLogic = $opertatorLogic;
    	return $this;
    }

    /**
     *
     * @return boolean
     */
    public function isLogicalAND()
    {
    	return $this->getOperatorLogic() == CriterionComposite::LOGICAL_AND;
    }

	/**
     *
     * @return boolean
     */
    public function isLogicalOR()
    {
    	return $this->getOperatorLogic() == CriterionComposite::LOGICAL_OR;
    }

}
