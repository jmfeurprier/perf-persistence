<?php

namespace perf\Persistence\Operation;

/**
 *
 *
 */
class Counter extends Operator
{

    /**
     *
     *
     * @param string $entityClass
     * @param null|\perf\Db\QueryFilter $filter
     * @return int
     */
    public function count($entityClass, \perf\Db\QueryFilter $filter = null)
    {
        $entityMetadata = $this->getEntityClassMetadata($entityClass);
        $entityStorage  = $entityMetadata->getStorage();
        $connectionId   = $entityStorage->getConnectionId();
        $connection     = $this->getConnection($connectionId);
        $table          = $entityStorage->getTable();
        $sqlTable       = $connection->escapeAndQuoteTable($table);

        $sql = "SELECT COUNT(0) FROM {$sqlTable}";

        if (is_null($filter)) {
            $parameters = array();
        } else {
            $sql .= ' WHERE ' . $filter->getClause();

            $parameters = $filter->getParameters();
        }

        return (int) $connection->queryValue($sql, $parameters);
    }
}
