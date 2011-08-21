QueryBuilder
============

QueryBuilder es un libreria con la cual puedes construir sentencias complejas de SQL de una manera facil.


BASIC COMMANDS
--------------


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

Podemos especificar las columnas a Seleccionar

    Query::create()
        ->addColumn('name')
        ->addColumn('email')
    ->from('jos_users');
    SELECT `name`, `email` FROM `jos_users`


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
------------


    Query::create()
        ->from('jos_content')
        ->setLimit(10)->setOffset(0);
    SELECT * FROM jos_content LIMIT 0, 10

Operadores Logicos
-----------------


    Query::create()
        ->from('jos_content')
        ->where()->add('state', 1)
            ->add('hits', 1000, Criterion::GREATER_THAN)
            ->add('hits', 2000, Criterion::LESS_THAN)
            ->add('created', '2009-08-20 00:00:00', Criterion::LESS_THAN);

    SELECT * FROM jos_content
    WHERE state = '1' 
        AND hits > '1000' 
        AND hits < '2000'
        AND created < '2009-08-20 00:00:00'

Puedes definir OR muy facilmente

    Query::create()->from('jos_content')
        ->where()
            ->add('created', '2009-08-20 00:00:00', Criterion::LESS_THAN)
            ->setOr()
                ->add('sectionid', 5)
                ->add('sectionid', 6)
            ->end()
        ->endWhere();

    SELECT * FROM jos_content
        WHERE created < '2009-08-20 00:00:00'
        AND (sectionid = '5' OR sectionid = '6')


---------


    SELECT *
    FROM jos_users
    WHERE username LIKE 'a%'


    SELECT id,email,usertype
    FROM jos_users
    WHERE username LIKE '%n'


    SELECT *
    FROM jos_users
    WHERE username LIKE '%m_n'

FUNCTIONS
---------


    SELECT AVG(hits) FROM jos_content
    
    SELECT COUNT(*) FROM jos_content
    
    SELECT MAX(hits) FROM jos_content
    
    SELECT SUM(hits) FROM jos_content


GROUP
----- 


    SELECT sectionid, SUM(hits)
    FROM jos_content
    GROUP BY sectionid


    SELECT sectionid, SUM(hits)
    FROM jos_content
    GROUP BY sectionid
    HAVING SUM(hits)>5000

NESTING
-------

Let's complicate things a bit more, SQL supports nesting. Suppose you want the rows from a table that have an attribute that is defined in another table, what would you do? For example, after you check the jos_content_frontpage table, how would you select all articles that are not presented in the front page? The IN clause is used to define a set of records.

    SELECT *
    FROM jos_content
    WHERE id NOT IN (SELECT content_id FROM jos_content_frontpage)

JOIN
----


    SELECT jos_categories.title, jos_content.title
    FROM jos_content
    JOIN jos_categories ON jos_content.catid = jos_categories.id
    ORDER BY jos_categories.id


    SELECT jos_categories.title, jos_content.title, jos_users.username
    FROM jos_content
    JOIN jos_categories ON jos_content.catid = jos_categories.id
    JOIN jos_users ON jos_content.created_by = jos_users.id
    ORDER BY jos_categories.id

