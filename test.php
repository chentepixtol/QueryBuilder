<?php

require_once 'QueryBuilder.php';

echo '<pre>';



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
			->add('nivel', array('linux', 'win', 'mac'))
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

$ini = time();
$i = 0;
while ($i <= 100000) {
	$i++;
	$qb->createSql();
}

$end = time();
echo "\n";
$sec = $end - $ini;
echo "En 100000 iteraciones sin Lazy Load tarda 201 seg \n";
echo "En 100000 iteraciones con Lazy Load tarda 38 seg\n";
echo "En 100000 iteraciones con Lazy Load en todas las partes tarda ".$sec." seg\n";