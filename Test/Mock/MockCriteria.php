<?php

use Query\Criteria;

class MockCriteria extends Criteria
{

    /**
     *
     * @return MockCriteria
     */
    public function newMethod(){
        return $this;
    }

}