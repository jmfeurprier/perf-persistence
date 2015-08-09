<?php

namespace perf\Persistence\Operation;

/**
 *
 *
 */
class Deleter extends Operator
{

    /**
     * Removes provided entity from storage.
     *
     * @param object $entity
     * @return void
     * @throws \DomainException
     * @throws \RuntimeException
     */
    public function delete($entity)
    {
        $entityMetadata = $this->getEntityMetadata($entity);
        $entityStorage  = $entityMetadata->getStorage();
        $connectionId   = $entityStorage->getConnectionId();
        $connection     = $this->getConnection($connectionId);
        $filter         = $entityMetadata->getPrimaryKey()->getFilter($connection, $entity);
        $table          = $entityStorage->getTable();
        $sqlTable       = $connection->escapeAndQuoteTable($table);
        $sqlWhere       = $filter->getClause();
        $parameters     = $filter->getParameters();

        $sql = "DELETE FROM {$sqlTable} WHERE {$sqlWhere}";

        $connection->execute($sql, $parameters);

        $entityMetadata->getPrimaryKey()->unbind($entity);
    }

    /**
     *
     *
     * @param string $entityClass
     * @param \perf\Db\QueryFilter $filter
     * @param null|\perf\Db\QuerySorting $sorting
     * @param null|int $limit
     * @return void
     * @throws \InvalidArgumentException
     * @throws \RuntimeException
     */
    public function deleteMany(
        $entityClass,
        \perf\Db\QueryFilter $filter,
        \perf\Db\QuerySorting $sorting = null,
        $limit = null
    ) {
        $entityMetadata = $this->getEntityClassMetadata($entityClass);
        $entityStorage  = $entityMetadata->getStorage();
        $connectionId   = $entityStorage->getConnectionId();
        $connection     = $this->getConnection($connectionId);
        $table          = $entityStorage->getTable();
        $sqlTable       = $connection->escapeAndQuoteTable($table);
        $sqlWhere       = $filter->getClause();
        $parameters     = $filter->getParameters();

        $sql = "DELETE FROM {$sqlTable} WHERE {$sqlWhere}";

        if (null !== $sorting) {
            $sqlOrderBy = $sorting->getClause();

            $sql .= " ORDER BY {$sqlOrderBy}";
        }

        if (null === $limit) {
            // No limit
        } elseif (is_int($limit)) {
            if ($limit < 1) {
                throw new \InvalidArgumentException('When provided, limit must be greater than zero.');
            }

            $sql .= " LIMIT {$limit}";
        } else {
            throw new \InvalidArgumentException('Limit must be an integer or null.');
        }

        $connection->execute($sql, $parameters);
    }
}
