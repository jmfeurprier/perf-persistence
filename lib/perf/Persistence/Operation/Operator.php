<?php

namespace perf\Persistence\Operation;

use \perf\Persistence\EntityMetadataPool;
use \perf\Db\ConnectionPool;
use \perf\Db\Connection;

/**
 *
 *
 */
abstract class Operator
{

    /**
     * Entity metadata pool.
     *
     * @var EntityMetadataPool
     */
    private $entityMetadataPool;

    /**
     * Connection pool.
     *
     * @var ConnectionPool
     */
    private $connectionPool;

    /**
     * Sets entity metadata pool.
     *
     * @param EntityMetadataPool $pool Entity metadata pool.
     * @return void
     */
    public function setEntityMetadataPool(EntityMetadataPool $pool)
    {
        $this->entityMetadataPool = $pool;
    }

    /**
     * Sets database connection pool.
     *
     * @param ConnectionPool $pool Database connection pool.
     * @return void
     */
    public function setConnectionPool(ConnectionPool $pool)
    {
        $this->connectionPool = $pool;
    }

    /**
     *
     *
     * @param object $entity
     * @return EntityMetadata
     * @throws \DomainException
     */
    protected function getEntityMetadata($entity)
    {
        $entityClass = get_class($entity);

        return $this->getEntityClassMetadata($entityClass);
    }

    /**
     *
     *
     * @param string $entityClass
     * @return EntityMetadata
     * @throws \DomainException
     */
    protected function getEntityClassMetadata($entityClass)
    {
        return $this->entityMetadataPool->fetch($entityClass);
    }

    /**
     *
     *
     * @param string $connectionId
     * @return Connection
     * @throws \DomainException
     */
    protected function getConnection($connectionId)
    {
        return $this->connectionPool->fetch($connectionId);
    }
}
