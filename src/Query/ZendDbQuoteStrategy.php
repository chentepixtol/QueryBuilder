<?php

namespace Query;

/**
 *
 * ZendDbQuoteStrategy
 *
 * @package Query
 * @copyright (c) Vicente Mendoza <chentepixtol@gmail.com>
 * @author chentepixtol
 *
 */
class ZendDbQuoteStrategy implements QuoteStrategy
{

    /**
     *
     *
     * @var Zend_Db_Adapter_Abstract
     */
    private $db;

    /**
     *
     * constructor
     * @param Zend_Db_Adapter_Abstract $db
     */
    public function __construct(\Zend_Db_Adapter_Abstract $db){
        $this->db = $db;
    }

    /**
     *
     *
     * @param mixed $value
     */
    public function quote($value)
    {
        if( $value instanceof Criterion ){
            $value = new Expression(' '.$value->createSql().' ');
        }
        if( $value instanceof Expression ){
            return $value->toString();
        }
        return $this->db->quote($value);
    }

    /**
     *
     *
     * @param mixed $value
     */
    public function quoteTable($value)
    {
        if( $value instanceof Criterion ){
            $value = new Expression('( '.$value->createSql().' )');
        }
        if( $value instanceof Expression ){
            return $value->toString();
        }
        return $this->db->quoteIdentifier($value);
    }

    /**
     *
     *
     * @param mixed $value
     */
    public function quoteColumn($value)
    {
        if( $value instanceof Criterion ){
            $value = new Expression('( '.$value->createSql().' )');
        }
        if( $value instanceof Expression ){
            return $value->toString();
        }
        $column = $this->db->quoteColumnAs($value, null);
        $column = str_replace('`*`', '*', $column);
        return $column;
    }

}
