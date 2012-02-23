<?php

namespace Test\Unit;

use Query\Query;
use Query\Expression\SQLCase;
use Query\ConditionalCriterion;
use Query\CriterionComposite;
use Query\ConditionalComposite;

class ExpressionseTest extends BaseTest
{

    /**
     *
     * @test
     */
    public function caseEspression()
    {
        $systems = array(
            1 => 'linux',
            2 => 'mac',
            3 => 'windows',
        );
        $sqlCase = new SQLCase('id_system', $systems);
        $this->assertEquals("CASE id_system WHEN '1' THEN 'linux' WHEN '2' THEN 'mac' WHEN '3' THEN 'windows' END", $sqlCase->toString());

        $sqlCase = new SQLCase('id_system', $systems, 'unknown');
        $sql = "CASE id_system WHEN '1' THEN 'linux' WHEN '2' THEN 'mac' WHEN '3' THEN 'windows' ELSE 'unknown' END";
        $this->assertEquals($sql, $sqlCase->toString());

        $query = Query::create($this->getZendDbQuoteStrategy())->addColumn($sqlCase, 'system_name')->from('systems');
        $this->assertEquals('SELECT '.$sql .' as `system_name` FROM `systems`' , $query->createSql());
    }

}