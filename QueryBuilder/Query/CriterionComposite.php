<?php

/**
 *
 * CriterionComposite
 *
 * @package Query
 * @copyright (c) Vicente Mendoza <chentepixtol@gmail.com>
 * @author chentepixtol
 *
 */
interface CriterionComposite extends Criterion
{

    const LOGICAL_AND = 'AND';
    const LOGICAL_OR = 'OR';

    /**
     *
     *
     * @param Criterion $criterion
     * @return Criterion
     */
    public function addCriterion(Criterion $criterion);

    /**
     *
     * @return CriterionComposite
     */
    public function getParent();

    /**
     *
     *
     * @param CriterionComposite $criterion
     * @return Criterion
     */
    public function setParent(CriterionComposite $criterion);

    /**
     *
     * @return int
     */
    public function count();

    /**
     *
     * @return boolean
     */
    public function isEmpty();

    /**
     *
     * @return array
     */
    public function getChildrens();

}

