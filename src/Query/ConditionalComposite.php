<?php

namespace Query;

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
        $this->refresh();
        $this->criterions[] = $criterion;
        $criterion->setQuoteStrategy($this->quoteStrategy);
        if( $criterion instanceof CriterionComposite )
            $criterion->setParent($this);
        return $this;
    }

    /**
     * (non-PHPdoc)
     * @see Query.Criterion::contains()
     */
    public function contains($element)
    {
        foreach ($this->getChildrens() as $child){
            if( $child->contains($element) ){
                return true;
            }
        }
        return false;
    }

    /**
     *
     * Cloning
     */
    public function __clone()
    {
        $copyCriterions = array();
        foreach ($this->criterions as $key => $criterion) {
            $copyCriterions[$key] = clone $criterion;
            if( $copyCriterions[$key] instanceof CriterionComposite ){
                $copyCriterions[$key]->setParent($this);
            }
        }
        $this->criterions = $copyCriterions;
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
        $number = 0;
        foreach( $this->getChildrens() as $criterion ){
            $number++;
            $sql .= $criterion->createSql();
            if( $total != $number ) $sql .= ' '. $this->getOperatorLogic() . ' ';
        }
        $this->sql = '( ' . $sql . ' )';
        return $this->sql;
    }

    /**
     * @return MongoQuery
     */
    public function createMongoQuery(){

    }

    /**
     * (non-PHPdoc)
     * @see Criterion::setQuoteStrategy()
     */
    public function setQuoteStrategy(QuoteStrategy $quoteStrategy){
        $this->quoteStrategy = $quoteStrategy;
        foreach ($this->getChildrens() as $children){
            $children->setQuoteStrategy($quoteStrategy);
        }
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
     * @return CriterionComposite
     */
    public function refresh(){
        $this->sql = null;
    }

    /**
     * (non-PHPdoc)
     * @see Query.CriterionComposite::remove()
     */
    public function remove($element)
    {
        foreach ($this->getChildrens() as $key => $child){
            if( $child->contains($element) ){
                $this->refresh();
                if( $child instanceof CriterionComposite ){
                    $child->remove($element);
                    if( $child->count() == 0 ){
                        unset($this->criterions[$key]);
                    }
                }else{
                    unset($this->criterions[$key]);
                }
            }
        }
        return $this;
    }

    /**
     * (non-PHPdoc)
     * @see Query.CriterionComposite::repace()
     */
    public function replace($element, Criterion $criterion)
    {
        foreach ($this->getChildrens() as $key => $child){
            if( $child->contains($element) ){
                $this->refresh();
                if( $child instanceof CriterionComposite ){
                    $child->replace($element, $criterion);
                }else{
                    $criterion->setQuoteStrategy($this->quoteStrategy);
                    $this->criterions[$key] = $criterion;
                }
            }
        }
        return $this;
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
