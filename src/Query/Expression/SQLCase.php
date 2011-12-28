<?php

namespace Query\Expression;

use Query\Expression;


/**
 *
 * SQLCase
 *
 * @package Query
 * @subpackage Expression
 * @copyright (c) Vicente Mendoza <chentepixtol@gmail.com>
 * @author chentepixtol
 *
 */
class SQLCase extends Expression
{

    /**
     *
     * @var string
     */
    private $expression = null;

    /**
     *
     * @var string
     */
    private $column;

    /**
     *
     * @var array
     */
    private $cases = array();

    /**
     *
     * @var string
     */
    private $default = null;

    /**
     *
     * construct
     * @param string $column
     * @param array $cases
     * @param mixed $default
     */
    public function __construct($column, $cases, $default = null)
    {
        $this->column = $column;
        $this->cases = $cases;
        $this->default = $default;
    }

    /**
     *
     * @return string
     */
    public function toString()
    {
        if( null == $this->expression ){
            $this->expression = 'CASE '. $this->column;
            foreach ($this->cases as $when => $then){
                $this->expression .= " WHEN '{$when}' THEN '{$then}'";
            }
            if( $this->default ){
                $this->expression .= " ELSE '{$this->default}'";
            }
            $this->expression .= " END";
        }
        return $this->expression;
    }
}
