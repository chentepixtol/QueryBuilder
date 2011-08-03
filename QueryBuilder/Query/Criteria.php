<?php

namespace Query;

/**
 *
 * Criteria
 *
 * @package Query
 * @copyright (c) Vicente Mendoza <chentepixtol@gmail.com>
 * @author chentepixtol
 *
 */
class Criteria implements Criterion
{

	/**
	 *
	 *
	 * @var ConditionalComposite
	 */
	protected $mainComposite;

	/**
	 *
	 *
	 * @var ConditionalComposite
	 */
	protected $currentComposite;

	/**
	 *
	 * @var QuoteStrategy
	 */
	protected $quoteStrategy;

	/**
	 *
	 * @var Query
	 */
	protected $query;

	/**
	 *
	 *
	 */
	public function __construct(Query $query = null){
		$this->query = $query;
		$this->currentComposite = $this->mainComposite = new ConditionalComposite();
	}

	/**
	 *
	 *
	 * @param string $column
	 * @param mixed $value
	 * @param string $comparison
	 * @param string $mutatorColumn
	 * @param string $mutatorValue
	 * @return Criteria
	 */
	public function add($column, $value, $comparison = Criterion::AUTO, $mutatorColumn = null, $mutatorValue = null)
	{
		$criterion = ConditionalCriterion::factory($column, $value, $comparison, $mutatorColumn, $mutatorValue);
		$this->currentComposite->addCriterion($criterion);
		return $this;
	}

	/**
	 *
	 * @return Criteria
	 */
	public function setAND()
	{
		if( $this->currentComposite->isEmpty() )
			$this->currentComposite->setOperatorLogic(CriterionComposite::LOGICAL_AND);
		elseif( $this->currentComposite->isLogicalOR() ){
			$composite = new ConditionalComposite();
			$this->currentComposite->addCriterion($composite);
			$this->currentComposite = $composite;
		}

		return $this;
	}

	/**
	 *
	 * @return Criteria
	 */
	public function setOR()
	{
		if( $this->currentComposite->isEmpty() )
			$this->currentComposite->setOperatorLogic(CriterionComposite::LOGICAL_OR);
		elseif( $this->currentComposite->isLogicalAND() ){
			$composite = new ConditionalComposite(CriterionComposite::LOGICAL_OR);
			$this->currentComposite->addCriterion($composite);
			$this->currentComposite = $composite;
		}
		return $this;
	}

	/**
	 *
	 * @return Criteria
	 */
	public function end()
	{
		$this->currentComposite = $this->currentComposite->getParent();
		return $this;
	}

	/**
	 *
	 * @return string
	 */
	public function createSql(){
		return $this->mainComposite->createSql();
	}

	/**
	 *
	 * @return boolean
	 */
	public function isEmpty(){
		return $this->mainComposite->isEmpty();
	}

	/**
	 *
	 *
	 * @param QuoteStrategy $quoteStrategy
	 */
	public function setQuoteStrategy(QuoteStrategy $quoteStrategy)
	{
		$this->quoteStrategy = $quoteStrategy;
		$this->currentComposite->setQuoteStrategy($quoteStrategy);
		$this->mainComposite->setQuoteStrategy($quoteStrategy);
		return $this;
	}

	/**
	 *
	 * @return Query
	 */
	public function getQuery(){
		return $this->query;
	}

	/**
	 *
	 * @return Query
	 */
	public function endWhere(){
		return $this->getQuery();
	}

	/**
	 *
	 * @return Query
	 */
	public function endHaving(){
		return $this->getQuery();
	}

	/**
	 *
	 * @return Query
	 */
	public function endJoin(){
		return $this->getQuery();
	}
}