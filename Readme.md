QueryBuilder [![Build Status](https://secure.travis-ci.org/chentepixtol/QueryBuilder.png?branch=master)](http://travis-ci.org/chentepixtol/QueryBuilder)
============

QueryBuilder es un libreria con la cual puedes construir sentencias complejas de SQL de una manera facil.


Comandos Basicos
----------------


Select basico.

    Query::create()->from('jos_users');        
    SELECT * FROM `jos_users`

Where con un condicional.

    Query::create()
        ->from('jos_content')
        ->where()->add('state', 1);
    SELECT * FROM `jos_content` WHERE `state` = '1'

con la condicion != 

    Query::create()
        ->from('jos_users')
        ->where()->add('username', 'admin', Criterion::NOT_EQUAL);
    SELECT * FROM `jos_users` WHERE `username` != 'admin'
    
    
O usando los alias

    Query::create()
        ->from('jos_users')
        ->where()->notEqual('username', 'admin');
    SELECT * FROM `jos_users` WHERE `username` != 'admin'


Podemos especificar las columnas a Seleccionar

    Query::create()
        ->addColumn('name')
        ->addColumn('email')
        ->from('jos_users');
    SELECT `name`, `email` FROM `jos_users`


Podemos agregar varias columnas al mismo tiempo

    Query::create()
        ->addColumns(array('name', 'email'))
        ->from('jos_users');
    SELECT `name`, `email` FROM `jos_users`
    
Podemos usar alias en las columnas

    Query::create()
        ->addColumn('created_at', 'my_date')
        ->from('jos_users');
    SELECT `created_at` as `my_date` FROM `jos_users`

Tambien se pueden usar funciones

    Query::create()
        ->addColumn('created_at', 'month', Criterion::MONTH)
        ->from('jos_users');
    SELECT MONTH(`created_at`) as `month` FROM `jos_users`


Ordernar datos
------------

Ordenar de manera Ascendente

    Query::create()
        ->from('jos_content')
        ->whereAdd('state', 1)
        ->orderBy('id');
    SELECT * FROM `jos_content` WHERE `state` ='1' ORDER BY `id` ASC

Y tambien Descendente

    Query::create()
        ->from('jos_content')
        ->whereAdd('state', 1)
        ->addDescendingOrderBy('created');
    SELECT * FROM `jos_content` WHERE `state` = '1' ORDER BY `created` DESC


Limitar el numero de registros
------------------------------

    Query::create()
        ->from('jos_content')
        ->setLimit(10)->setOffset(0);
    SELECT * FROM `jos_content` LIMIT 0, 10

Operadores Logicos
------------------

    Query::create()
        ->from('jos_content')
        ->where()->add('state', 1)
            ->add('hits', 1000, Criterion::GREATER_THAN)
            ->add('hits', 2000, Criterion::LESS_THAN)
            ->add('created', '2009-08-20 00:00:00', Criterion::LESS_THAN);

    SELECT * FROM `jos_content`
    WHERE `state` = '1' 
        AND `hits` > '1000' 
        AND `hits` < '2000'
        AND `created` < '2009-08-20 00:00:00'

Puedes definir OR muy facilmente

    Query::create()->from('jos_content')
        ->where()
            ->add('created', '2009-08-20 00:00:00', Criterion::LESS_THAN)
            ->setOr()
                ->add('sectionid', 5)
                ->add('sectionid', 6)
            ->end()
        ->endWhere();

    SELECT * FROM `jos_content`
        WHERE `created` < '2009-08-20 00:00:00'
        AND (`sectionid` = '5' OR `sectionid` = '6')


---------

Existen diferentes comparaciones disponibles, una de esas es LIKE

    Query::create()
        ->from('jos_users')
        ->where()->add('username', 'a', Criterion::LEFT_LIKE);

    SELECT *
    FROM `jos_users`
    WHERE `username` LIKE 'a%'

    Query::create()
        ->from('jos_users')
        ->where()->add('username', 'a', Criterion::RIGHT_LIKE);

    SELECT *
    FROM `jos_users`
    WHERE `username` LIKE '%a'


Funciones Mysql
---------

    Query::create()->from('jos_content')->addColumn('hits', 'average', Criterion::AVG);
    SELECT AVG(`hits`) as `average` FROM `jos_content`
    
    Query::create()->from('jos_content')->addColumn('*', 'count', Criterion::COUNT);
    SELECT COUNT(*) as count FROM jos_content
    
    Query::create()->from('jos_content')->addColumn('hits', 'max', Criterion::COUNT);
    SELECT MAX(`hits`) as `max` FROM `jos_content`
    
    Query::create()->from('jos_content')->addColumn('hits', 'max', Criterion::SUM);
    SELECT SUM(`hits`) FROM `jos_content`


Grupos
----- 

Podemos agrupar por columnas

    Query::create()
        ->addColumn('sectionid')
        ->addColumn('hits', 'sum', Criterion::SUM)
        ->from('jos_content')
        ->addGroupBy('sectionid');

    SELECT `sectionid`, SUM(`hits`) as sum
        FROM `jos_content`
        GROUP BY `sectionid`

Podemos agregar condiciones a la Clausula HAVING

    Query::create()
        ->addColumn('sectionid')
        ->addColumn('hits', 'sum', Criterion::SUM)
        ->from('jos_content')
        ->addGroupBy('sectionid')
        ->having()->add('sum', 5000, Criterion::GREATHER_THAN);

    SELECT `sectionid`, SUM(`hits`) as `sum`
        FROM `jos_content`
        GROUP BY `sectionid`
        HAVING `sum` > 5000

Subconsultas
-------

Podemos definir consultas dentro de otras consultas

    $subquery = Query::create()
        ->from('jos_content_frontpage');
    Query::create()
        ->from('jos_content')
        ->where()->add('id', $subquery, Criterion::NOT_IN);

    SELECT *
        FROM `jos_content`
        WHERE `id` NOT IN (
            SELECT `content_id` FROM jos_content_frontpage
        )

JOINS
----

Podemos definir cruces entre tablas mediante Joins.

    Query::create()
        ->addColumn('jos_categories.title')
        ->addColumn('jos_content.title')
        ->from('jos_content')
        ->joinOn('jos_categories')
            ->add('jos_content.catid', 'jos_categories.id', Criterion::EQUAL, null, Criterion::AS_FIELD);

    SELECT `jos_categories.title`, `jos_content.title`
        FROM `jos_content`
    JOIN `jos_categories` ON `jos_content`.`catid` = `jos_categories`.`id`
    
Podemos tener consultas preparadas y luego proporcionar los valores.

Con el comodin ':'

    Query::create()
        ->from('users')
        ->where()->add('id', ':idUser')
        ->bind(array('idUser'=> 1));
        
    SELECT * FROM `users` WHERE `id` = 1;
        
Con el comodin '?'
    
    Query::create()
        ->from('users')
        ->where()->add('id', '?')
        ->bind(array(1));    

    SELECT * FROM `users` WHERE `id` = 1;









