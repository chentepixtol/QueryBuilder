<?php

namespace Query;

/**
 *
 * Delete
 *
 * @package Query
 * @copyright (c) Vicente Mendoza <chentepixtol@gmail.com>
 * @author chentepixtol
 */
class Delete extends ManipulationStatement implements Criterion
{

    /**
     *
     * @return string
     */
    public function createSql(){

        $parts = array(
            $this->createFromSql(),
            $this->createJoinSql(),
            $this->createWhereSql(),
            $this->createLimitSql(),
        );

        $sql = "DELETE " . implode(' ', array_filter($parts));

        return $this->replaceParameters($sql);
    }

    /**
     * @return MongoQuery
     */
    public function createMongoQuery(){

    }

}
