<?php

interface Criterion
{
	  // Comparision
      const EQUAL = '=';
      const JUST_LIKE = 'LIKE'; # table.field LIKE '{value}'
      const LIKE = ' LIKE '; # table.field LIKE '%{value}%'
      const LEFT_LIKE = ' LIKE'; # table.field LIKE '%{value}'
      const RIGHT_LIKE = 'LIKE '; # table.field LIKE '{value}%'
      const NOT_LIKE = ' NOT_LIKE '; # table.field NOT LIKE '%{value}%'
      const NOT_JUST_LIKE = 'NOT_LIKE'; # table.field NOT LIKE '{value}'
      const IN = 'IN';
      const NOT_IN = 'NOT IN';
      const NOT_EQUAL = '!=';
      const GREATER_THAN = '>';
      const LESS_THAN = '<';
      const GREATER_OR_EQUAL = '>=';
      const LESS_OR_EQUAL = '<=';
      const BETWEEN = 'BETWEEN';
      const IS_NULL = 'IS NULL';
      const IS_NOT_NULL = 'IS NOT NULL';
      const AUTO = 'AUTO';

      //MUTATORS
      const PASSWORD = 'PASSWORD(%s)';
      const LOWER = 'LOWER(%s)';
      const UPPER = 'UPPER(%s)';
      const DATE = 'DATE(%s)';
      const MONTH = 'MONTH(%s)';
      const YEAR = 'YEAR(%s)';
      const TRIM = 'TRIM(%s)';

      //JOINS
      const INNER_JOIN = 'INNER JOIN';
      const LEFT_JOIN = 'LEFT JOIN';
      const RIGHT_JOIN = 'RIGHT JOIN';

      /**
       *
       * @return string
       */
      public function createSql();

      /**
       *
       * @param QuoteStrategy $quoteStrategy
       */
      public function setQuoteStrategy(QuoteStrategy $quoteStrategy);

}

