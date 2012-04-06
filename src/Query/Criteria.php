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
     * @var boolean
     */
    protected $logicalSetter;

    /**
     *
     * @var string
     */
    protected $prefix;

    /**
     *
     *
     */
    public function __construct(ManipulationStatement $query = null){
        $this->setQuery($query);
        $this->currentComposite = $this->mainComposite = new ConditionalComposite();
        $this->setQuoteStrategy(new NullQuoteStrategy());
    }

    /**
     *
     * Cloning
     */
    public function __clone()
    {
        $this->mainComposite = clone $this->mainComposite;
        $this->root();
    }

    /**
     *
     * @param string $table
     * @return Criteria
     */
    public function prefix($alias){
        $this->prefix = $alias;
        return $this;
    }

    /**
     *
     * @param string $table
     * @return Criteria
     */
    public function endPrefix(){
        $this->prefix = null;
        return $this;
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
        $column = $this->addPrefix($column);
        $criterion = ConditionalCriterion::factory($column, $value, $comparison, $mutatorColumn, $mutatorValue);
        $this->addCriterion($criterion);
        return $this;
    }



    /**
     * (non-PHPdoc)
     * @see Query.Criterion::contains()
     */
    public function contains($element){
        return $this->mainComposite->contains($element);
    }

    /**
     *
     * @param string $element
     * @return Criteria
     */
    public function remove($element){
        $this->mainComposite->remove($element);
        $this->root();
        return $this;
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
    public function replace($column, $value, $comparison = Criterion::AUTO, $mutatorColumn = null, $mutatorValue = null)
    {
        $criterion = ConditionalCriterion::factory($column, $value, $comparison, $mutatorColumn, $mutatorValue);
        $this->mainComposite->replace($column, $criterion);
        $this->root();
        return $this;
    }

    /**
     *
     *
     * @param string $field1
     * @param string $field2
     * @return Criteria
     */
    public function equalFields($field1, $field2){
        $this->add($field1, $field2, Criterion::EQUAL, null, Criterion::AS_FIELD);
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
     *
     *
     * @param string $column
     * @param mixed $value
     * @param string $mutatorColumn
     * @param string $mutatorValue
     * @return Criteria
     */
    public function greaterThan($column, $value, $mutatorColumn = null, $mutatorValue = null){
        $this->add($column, $value, Criterion::GREATER_THAN, $mutatorColumn, $mutatorValue);
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
    public function lessThan($column, $value, $mutatorColumn = null, $mutatorValue = null){
        $this->add($column, $value, Criterion::LESS_THAN, $mutatorColumn, $mutatorValue);
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
    public function greaterOrEqual($column, $value, $mutatorColumn = null, $mutatorValue = null){
        $this->add($column, $value, Criterion::GREATER_OR_EQUAL, $mutatorColumn, $mutatorValue);
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
    public function lessOrEqual($column, $value, $mutatorColumn = null, $mutatorValue = null){
        $this->add($column, $value, Criterion::LESS_OR_EQUAL, $mutatorColumn, $mutatorValue);
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
            $column = $this->getByKey($add, 0);
            $value = $this->getByKey($add, 1);
            $comparison = $this->getByKey($add, 2, Criterion::AUTO);
            $mutatorColumn = $this->getByKey($add, 3);
            $mutatorValue = $this->getByKey($add, 4);
            $this->add($column, $value, $comparison, $mutatorColumn, $mutatorValue);
        }
        return $this;
    }

    /**
     *
     * otiene por el indice
     * @param array $array
     * @param int $key
     * @param mixed $default
     * @return mixed
     */
    protected function getByKey($array, $key, $default = null){
        return isset($array[$key]) ? $array[$key] : $default;
    }

    /**
     *
     * @return Criteria
     */
    public function setAND()
    {
        if( $this->currentComposite->isEmpty() && !$this->logicalSetter )
            $this->currentComposite->setOperatorLogic(CriterionComposite::LOGICAL_AND);
        elseif( $this->currentComposite->isLogicalOR() ){
            $composite = new ConditionalComposite();
            $this->currentComposite->addCriterion($composite);
            $this->currentComposite = $composite;
        }
        $this->logicalSetter = true;
        return $this;
    }

    /**
     *
     * @return Criteria
     */
    public function setOR()
    {
        if( $this->currentComposite->isEmpty() && !$this->logicalSetter )
            $this->currentComposite->setOperatorLogic(CriterionComposite::LOGICAL_OR);
        elseif( $this->currentComposite->isLogicalAND() ){
            $composite = new ConditionalComposite(CriterionComposite::LOGICAL_OR);
            $this->currentComposite->addCriterion($composite);
            $this->currentComposite = $composite;
        }
        $this->logicalSetter = true;
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
     * @return MongoQuery
     */
    public function createMongoQuery(){

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
     * seek to root
     * @return Criteria
     */
    public function root(){
        $this->currentComposite = $this->mainComposite;
        return $this;
    }

    /**
     *
     *
     * @param Criterion $criterion
     * @return Criteria
     */
    public function addCriterion(Criterion $criterion){
        $this->mainComposite->refresh();
        $this->currentComposite->addCriterion($criterion);
        return $this;
    }

    /**
     *
     *
     * @param ManipulationStatement $query
     * @return Criteria
     */
    public function setQuery(ManipulationStatement $query = null){
        $this->query = $query;
    }

    /**
     *
     * @param string $column
     */
    protected function addPrefix($column){
        if( null != $this->prefix ){
            if( is_string($column) ){
                $column = $this->prefix . '.' . $column;
            }
        }
        return $column;
    }

}