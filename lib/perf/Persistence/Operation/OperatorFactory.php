<?php

namespace perf\Persistence\Operation;

use \perf\Persistence\EntityMetadataPool;
use \perf\Db\ConnectionPool;

/**
 *
 *
 */
class OperatorFactory
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
     * Entity importer.
     *
     * @var EntityImporter
     */
    private $entityImporter;

    /**
     * Entity exporter.
     *
     * @var EntityExporter
     */
    private $entityExporter;

    /**
     *
     *
     * @param EntityMetadataPool $pool
     * @return void
     */
    public function setEntityMetadataPool(EntityMetadataPool $pool)
    {
        $this->entityMetadataPool = $pool;
    }

    /**
     *
     *
     * @param ConnectionPool $connectionPool
     * @return void
     */
    public function setConnectionPool(ConnectionPool $pool)
    {
        $this->connectionPool = $pool;
    }

    /**
     *
     *
     * @param EntityImporter $importer
     * @return void
     */
    public function setEntityImporter(EntityImporter $importer)
    {
        $this->entityImporter = $importer;
    }

    /**
     *
     *
     * @param EntityExporter $exporter
     * @return void
     */
    public function setEntityExporter(EntityExporter $exporter)
    {
        $this->entityExporter = $exporter;
    }

    /**
     *
     *
     * @return Inserter
     */
    public function getInserter()
    {
        static $inserter;

        if (!$inserter) {
            $inserter = new Inserter();

            $inserter->setEntityMetadataPool($this->entityMetadataPool);
            $inserter->setConnectionPool($this->connectionPool);
            $inserter->setEntityExporter($this->getEntityExporter());
        }

        return $inserter;
    }

    /**
     *
     *
     * @return Updater
     */
    public function getUpdater()
    {
        static $updater;

        if (!$updater) {
            $updater = new Updater();

            $updater->setEntityMetadataPool($this->entityMetadataPool);
            $updater->setConnectionPool($this->connectionPool);
            $updater->setEntityExporter($this->getEntityExporter());
        }

        return $updater;
    }

    /**
     *
     *
     * @return EntityExporter
     */
    private function getEntityExporter()
    {
        if (!$this->entityExporter) {
            $this->setEntityExporter(new EntityExporter());
        }

        return $this->entityExporter;
    }

    /**
     *
     *
     * @return Deleter
     */
    public function getDeleter()
    {
        static $deleter;

        if (!$deleter) {
            $deleter = new Deleter();

            $deleter->setEntityMetadataPool($this->entityMetadataPool);
            $deleter->setConnectionPool($this->connectionPool);
        }

        return $deleter;
    }

    /**
     *
     *
     * @return Selecter
     */
    public function getSelecter()
    {
        static $selecter;

        if (!$selecter) {
            $selecter = new Selecter();

            $selecter->setEntityMetadataPool($this->entityMetadataPool);
            $selecter->setConnectionPool($this->connectionPool);
            $selecter->setEntityImporter($this->getEntityImporter());
        }

        return $selecter;
    }

    /**
     *
     *
     * @return EntityImporter
     */
    private function getEntityImporter()
    {
        if (!$this->entityImporter) {
            $this->setEntityImporter(new EntityImporter());
        }

        return $this->entityImporter;
    }

    /**
     *
     *
     * @return Counter
     */
    public function getCounter()
    {
        static $counter;

        if (!$counter) {
            $counter = new Counter();

            $counter->setEntityMetadataPool($this->entityMetadataPool);
            $counter->setConnectionPool($this->connectionPool);
        }

        return $counter;
    }
}
