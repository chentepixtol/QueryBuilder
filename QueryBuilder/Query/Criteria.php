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
		$this->setQuoteStrategy(new NullQuoteStrategy());
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
		$this->addCriterion($criterion);
		return $this;
	}

	/**
	 *
	 *
	 * @param string $column
	 * @param mixed $value
	 * @param string $mutatorColumn
	 * @param string $mutatorValue
	 * @return Criteria
	 */
	public function equal($column, $value, $mutatorColumn = null, $mutatorValue = null){
		$this->add($column, $value, Criterion::EQUAL, $mutatorColumn, $mutatorValue);
		return $this;
	}

	/**
	 *
	 *
	 * @param string $column
	 * @param mixed $value
	 * @param string $mutatorColumn
	 * @param string $mutatorValue
	 * @return Criteria
	 */
	public function notEqual($column, $value, $mutatorColumn = null, $mutatorValue = null){
		$this->add($column, $value, Criterion::NOT_EQUAL, $mutatorColumn, $mutatorValue);
		return $this;
	}

	/**
	 *
	 *
	 * @param string $column
	 * @param mixed $value
	 * @param string $mutatorColumn
	 * @param string $mutatorValue
	 * @return Criteria
	 */
	public function justlike($column, $value, $mutatorColumn = null, $mutatorValue = null){
		$this->add($column, $value, Criterion::JUST_LIKE, $mutatorColumn, $mutatorValue);
		return $this;
	}

	/**
	 *
	 *
	 * @param string $column
	 * @param mixed $value
	 * @param string $mutatorColumn
	 * @param string $mutatorValue
	 * @return Criteria
	 */
	public function like($column, $value, $mutatorColumn = null, $mutatorValue = null){
		$this->add($column, $value, Criterion::LIKE, $mutatorColumn, $mutatorValue);
		return $this;
	}

	/**
	 *
	 *
	 * @param string $column
	 * @param mixed $value
	 * @param string $mutatorColumn
	 * @param string $mutatorValue
	 * @return Criteria
	 */
	public function in($column, $value, $mutatorColumn = null, $mutatorValue = null){
		$this->add($column, $value, Criterion::IN, $mutatorColumn, $mutatorValue);
		return $this;
	}

	/**
	 *
	 *
	 * @param string $column
	 * @param mixed $value
	 * @param string $mutatorColumn
	 * @param string $mutatorValue
	 * @return Criteria
	 */
	public function range($column, $value, $mutatorColumn = null, $mutatorValue = null){
		$this->add($column, $value, Criterion::RANGE, $mutatorColumn, $mutatorValue);
		return $this;
	}

	/**
	 *
	 *
	 * @param string $column
	 * @param mixed $value
	 * @param string $mutatorColumn
	 * @param string $mutatorValue
	 * @return Criteria
	 */
	public function notIn($column, $value, $mutatorColumn = null, $mutatorValue = null){
		$this->add($column, $value, Criterion::NOT_IN, $mutatorColumn, $mutatorValue);
		return $this;
	}

	/**
	 *
	 *
	 * @param string $column
	 * @param string $mutatorColumn
	 * @param string $mutatorValue
	 * @return Criteria
	 */
	public function isNull($column, $mutatorColumn = null, $mutatorValue = null){
		$this->add($column, null, Criterion::IS_NULL, $mutatorColumn, $mutatorValue);
		return $this;
	}

	/**
	 *
	 *
	 * @param string $column
	 * @param string $mutatorColumn
	 * @param string $mutatorValue
	 * @return Criteria
	 */
	public function isNotNull($column, $mutatorColumn = null, $mutatorValue = null){
		$this->add($column, null, Criterion::IS_NOT_NULL, $mutatorColumn, $mutatorValue);
		return $this;
	}

	/**
	 * multipleAdd
	 * @param array $adds
	 * @return Criteria
	 */
	public function multipleAdd($adds)
	{
		foreach ($adds as $add){
			$column = isset($add[0]) ? $add[0] : null;
			$value = isset($add[1]) ? $add[1] : null;
			$comparison = isset($add[2]) ? $add[2] : Criterion::AUTO;
			$mutatorColumn = isset($add[3]) ? $add[3] : null;
			$mutatorValue = isset($add[4]) ? $add[4] : null;
			$this->add($column, $value, $comparison, $mutatorColumn, $mutatorValue);
		}
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
	 * @return int
	 */
	public function count(){
		return $this->mainComposite->count();
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

	/**
	 *
	 * @return Query\ConditionalComposite
	 */
	public function getComposite(){
		return $this->mainComposite;
	}

	/**
	 *
	 * Merge two criterias
	 * @param Criteria $criteria
	 * @return Criteria
	 */
	public function merge(Criteria $criteria)
	{
		$composite = $criteria->getComposite();
		$composite->isLogicalOR() ? $this->setOR() : $this->setAND();

		foreach ( $composite->getChildrens() as $children ){
			$this->addCriterion($children);
		}
		return $this;
	}

	/**
	 *
	 *
	 * @param Criterion $criterion
	 * @return Criteria
	 */
	public function addCriterion(Criterion $criterion){
		$this->currentComposite->addCriterion($criterion);
		return $this;
	}
}