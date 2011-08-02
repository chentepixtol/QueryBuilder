<?php

require_once 'Application/Query/Criterion.php';
require_once 'Application/Query/CriterionComposite.php';
require_once 'Application/Query/SelectCriterion.php';
require_once 'Application/Query/QuoteStrategy.php';

require_once 'Application/Query/Range.php';
require_once 'Application/Query/ConditionalCriterion.php';
require_once 'Application/Query/AutoConditionalCriterion.php';
require_once 'Application/Query/ConditionalComposite.php';

require_once 'Application/Query/Criteria.php';
require_once 'Application/Query/SimpleQuoteStrategy.php';
require_once 'Application/Query/ZendDbQuoteStrategy.php';
require_once 'Application/Query/Query.php';


echo '<pre>';



$query = new Query();
$query->addColumn('myColumn', 'alias1')
	->addColumn('myColumn2')
	->from('mytable')
	->innerJoinOn('mytabl2')
		->add('mytable.id', 'mytabl2.id_2', Criterion::EQUAL, null, Criterion::AS_FIELD)
	->endJoin()
	->leftJoinOn('person')
		->add('person.id', 'user.id_person', Criterion::EQUAL, null, Criterion::AS_FIELD)
	->endJoin()
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
	->having()
		->add('groupcolumn', '123')
		->add('group2', '45')
	->endHaving()
	->setLimit(1)
	->addDescendingOrderByColumn('myColumn2')
;

echo $query->createBeautySql();
