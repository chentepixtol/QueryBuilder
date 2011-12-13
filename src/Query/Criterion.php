<?php

namespace Query;

/**
 *
 * Criterion
 *
 * @package Query
 * @copyright (c) Vicente Mendoza <chentepixtol@gmail.com>
 * @author chentepixtol
 *
 */
interface Criterion extends Mutator, Cmp
{
      //JOINS
      const JOIN = 'JOIN';
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

      /**
       *
       * @param mixed $element
       * @return $boolean
       */
      public function contains($element);

}

