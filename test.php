<?php

require_once 'Query.php';

echo '<pre>';



$qb = new Query();
$qb->addColumn('myColumn', 'alias1')
	->addColumn('myColumn2')
	->from('mytable')
	->innerJoinOn('mytabl2', 'mytable.id = mytabl2.id_2')
	->where()
		->add('nivel', '1')
	   	->add('nivel', '1')
		->setOR()
			->add('nivel', '2')
			->add('nivel', '2')
			->setAND()
				->add('nivel', '3')
				->add('nivel', '3')
				->add('nivel', array('linux', 'win', 'mac'))
			->end()
			->add('nivel', '2')
			->add('nivel', '2')
		->end()
		->add('nivel', '1')
		->add('nivel', '1')
	->endWhere()
	->addGroupByColumn('alias1')
	/*->having()
		->add('groupcolumn', '123')
		->add('group2', '45')
	->endHaving()*/
	->setLimit(1)
	->addDescendingOrderByColumn('myColumn2')
;


echo $qb->createSql();
