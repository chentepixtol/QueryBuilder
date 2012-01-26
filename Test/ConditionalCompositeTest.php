<?php

use Query\ConditionalCriterion;
use Query\CriterionComposite;
use Query\ConditionalComposite;


require_once 'BaseTest.php';

class ConditionalCompositeTest extends BaseTest
{

    /**
     *
     * @test
     */
    public function containsTest(){
        $composite = $this->getlogicalAnd();
        $this->assertFalse($composite->contains('username'));
        $this->assertTrue($composite->contains('col1'));
        $this->assertTrue($composite->contains('col2'));
    }

    /**
     *
     * @test
     */
    public function removeTest(){
        $composite = $this->getlogicalAnd();
        $composite->remove('col1');
        $this->assertFalse($composite->contains('col1'));
        $this->assertTrue($composite->contains('col2'));
        $this->assertEquals(1, $composite->count());
    }

    /**
     *
     */
    public function getlogicalAnd()
    {
        $composite = new ConditionalComposite();
        $composite->setQuoteStrategy($this->getZendDbQuoteStrategy());

        $this->assertEquals(CriterionComposite::LOGICAL_AND, $composite->getOperatorLogic());
        $this->assertEquals(0, $composite->count());
        $this->assertTrue($composite->isEmpty());
        $this->assertEquals("( 1 )", $composite->createSql());

        $criterion1 = new ConditionalCriterion('col1', 'val1');
        $criterion2 = new ConditionalCriterion('col2', 'val2');

        $composite->addCriterion($criterion1);
        $composite->addCriterion($criterion2);

        $childrens = $composite->getChildrens();
        $this->assertTrue($childrens[0] === $criterion1);
        $this->assertTrue($childrens[1] === $criterion2);
        $this->assertEquals(2, $composite->count());
        $this->assertEquals("( {$criterion1->createSql()} AND {$criterion2->createSql()} )", $composite->createSql());

        return $composite;
    }

    /**
     *
     */
    public function getlogicalOr()
    {
        $composite = new ConditionalComposite(CriterionComposite::LOGICAL_OR);
        $composite->setQuoteStrategy($this->getZendDbQuoteStrategy());

        $this->assertEquals(CriterionComposite::LOGICAL_OR, $composite->getOperatorLogic());
        $this->assertEquals(0, $composite->count());
        $this->assertTrue($composite->isEmpty());
        $this->assertEquals("( 1 )", $composite->createSql());

        $criterion1 = new ConditionalCriterion('col1', 'val1');
        $criterion2 = new ConditionalCriterion('col2', 'val2');

        $composite->addCriterion($criterion1);
        $composite->addCriterion($criterion2);

        $childrens = $composite->getChildrens();
        $this->assertTrue($childrens[0] === $criterion1);
        $this->assertTrue($childrens[1] === $criterion2);
        $this->assertEquals(2, $composite->count());
        $this->assertEquals("( {$criterion1->createSql()} OR {$criterion2->createSql()} )", $composite->createSql());

        return $composite;
    }

    /**
     * @test
     */
    public function nested()
    {
        $composite = new ConditionalComposite(CriterionComposite::LOGICAL_OR);
        $composite->setQuoteStrategy($this->getZendDbQuoteStrategy());

        $this->assertEquals(CriterionComposite::LOGICAL_OR, $composite->getOperatorLogic());
        $this->assertEquals(0, $composite->count());
        $this->assertTrue($composite->isEmpty());
        $this->assertEquals("( 1 )", $composite->createSql());

        $composite1 = $this->getlogicalAnd();
        $composite2 = $this->getlogicalOr();

        $composite->addCriterion($composite1);
        $composite->addCriterion($composite2);

        $childrens = $composite->getChildrens();
        $this->assertTrue($childrens[0] === $composite1);
        $this->assertTrue($childrens[1] === $composite2);
        $this->assertTrue($composite1->getParent() === $composite);
        $this->assertTrue($composite2->getParent() === $composite);
        $this->assertEquals(2, $composite->count());
        $this->assertEquals("( {$composite1->createSql()} OR {$composite2->createSql()} )", $composite->createSql());
    }


    /**
     * @test
     */
    public function threeAnd()
    {
        $composite = new ConditionalComposite();
        $composite->setQuoteStrategy($this->getZendDbQuoteStrategy());

        $this->assertEquals(CriterionComposite::LOGICAL_AND, $composite->getOperatorLogic());
        $this->assertEquals(0, $composite->count());
        $this->assertEquals("( 1 )", $composite->createSql());

        $criterion1 = new ConditionalCriterion('col1', 'val1');
        $criterion2 = new ConditionalCriterion('col2', 'val2');
        $criterion3 = new ConditionalCriterion('col3', 'val3');

        $composite->addCriterion($criterion1);
        $composite->addCriterion($criterion2);
        $composite->addCriterion($criterion3);

        $childrens = $composite->getChildrens();
        $this->assertTrue($childrens[0] === $criterion1);
        $this->assertTrue($childrens[1] === $criterion2);
        $this->assertTrue($childrens[2] === $criterion3);
        $this->assertEquals(3, $composite->count());
        $this->assertEquals("( {$criterion1->createSql()} AND {$criterion2->createSql()} AND {$criterion3->createSql()} )", $composite->createSql());

        return $composite;
    }


}