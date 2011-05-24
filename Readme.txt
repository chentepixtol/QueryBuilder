SELECT [DISTINCT] column || ( criterion ) || MUTATOR(column) [as alias]  , ... 
FROM tablename [as alias]
[ INNER || LEFT || RIGTH ] JOIN tablename [as alias] 
    ON ( criterion )
WHERE ( criterion )
GROUP BY column, ...
HAVING ( criterion )
ORDER BY column [ ASC || DESC ], ...
LIMIT row_count [OFFSET offset]
;


conditional
MUTATOR(column) || column   Comparacion  (valor || MUTATOR(valor))
