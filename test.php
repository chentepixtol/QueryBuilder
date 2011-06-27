<?php

require_once 'QueryBuilder.php';

$qb = new QueryBuilder();
$qb->addColumn('myColumn', 'alias1')
	->addColumn('myColumn2')
	->from('mytable')
	->join('mytabl2', 'mytable.id = mytabl2.id_2')
	->add('nivel', '1')
   	->add('nivel', '1')
	->setOR()
		->add('nivel', '2')
		->add('nivel', '2')
		->setAND()
			->add('nivel', '3')
			->add('nivel', '3')
			->add('nivel', array('linux', 'win', 'mac'), Criterion::IN)
		->end()
		->add('nivel', '2')
		->add('nivel', '2')
	->end()
	->add('nivel', '1')
	->add('nivel', '1')
	->addGroupByColumn('alias1')
	->setLimit(1)
	->addDescendingOrderByColumn('myColumn2')
;

echo '<pre>';
echo $qb->createSql();