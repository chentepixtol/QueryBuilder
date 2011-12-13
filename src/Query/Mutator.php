<?php

namespace Query;

/**
 *
 * Mutator
 *
 * @package Query
 * @copyright (c) Vicente Mendoza <chentepixtol@gmail.com>
 * @author chentepixtol
 *
 */
interface Mutator
{
    const PASSWORD = 'PASSWORD(%s)';
    const LOWER = 'LOWER(%s)';
    const UPPER = 'UPPER(%s)';
    const TRIM = 'TRIM(%s)';
    const DATE = 'DATE(%s)';
    const YEAR = 'YEAR(%s)';
    const MONTH = 'MONTH(%s)';
    const DAY = 'DAY(%s)';
    const COUNT = 'COUNT(%s)';
    const AS_FIELD = 'AS_FIELD';
    const AS_EXPRESSION = 'AS_EXPRESSION';
}

