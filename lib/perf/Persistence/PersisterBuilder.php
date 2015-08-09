<?php

namespace perf\Persistence;

use \perf\Db\ConnectionPool;
use \perf\Db\QueryFilter;
use \perf\Db\QuerySorting;

/**
 *
 *
 */
class PersisterBuilder
{

    /**
     *
     *
     * @var ConnectionPool
     */
    private $connectionPool;

    /**
     *
     *
     * @var \perf\Caching\CacheClient
     */
    private $cacheClient;

    /**
     *
     *
     * @param ConnectionPool $connectionPool
     * @return PersisterBuilder Fluent return.
     */
    public function setConnectionPool(ConnectionPool $connectionPool)
    {
        $this->connectionPool = $connectionPool;

        return $this;
    }

    /**
     *
     *
     * @param \perf\Caching\CacheClient $client
     * @return PersisterBuilder Fluent return.
     */
    public function setCacheClient(\perf\Caching\CacheClient $client)
    {
        $this->cacheClient = $client;

        return $this;
    }

    /**
     *
     *
     * @return Persister
     */
    public function build()
    {
        if (null === $this->cacheClient) {
            $cacheStorage = new \perf\Caching\VolatileStorage();

            $cacheClient = new \perf\Caching\CacheClient($cacheStorage);
        } else {
            $cacheClient = $this->cacheClient;
        }

        $entityMetadataPool = new EntityMetadataPool();
        $entityMetadataPool->setCacheClient($cacheClient);

        $operatorFactory = new \perf\Persistence\Operation\OperatorFactory();
        $operatorFactory->setEntityMetadataPool($entityMetadataPool);
        $operatorFactory->setConnectionPool($this->connectionPool);

        $persister = new Persister();
        $persister->setOperatorFactory($operatorFactory);

        return $persister;
    }
}
