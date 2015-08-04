<?php

namespace perf\Persistence\Operation;

use \perf\Persistence\EntityMetadataPool;
use \perf\Db\ConnectionPool;
use \perf\Persistence\Column;
use \perf\Persistence\PrimaryKey;

/**
 *
 *
 */
class Inserter extends Operator
{

    /**
     *
     *
     * @var EntityExporter
     */
    private $entityExporter;

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
     * Inserts provided entity into storage.
     *
     * @param object $entity
     * @return void
     * @throws \DomainException
     * @throws \RuntimeException
     */
    public function insert($entity)
    {
        $entityMetadata = $this->getEntityMetadata($entity);
        $storage        = $entityMetadata->getStorage();
        $connectionId   = $storage->getConnectionId();
        $connection     = $this->getConnection($connectionId);
        $dbTable        = $storage->getTable();

        $row = $this->entityExporter->export($entityMetadata, $entity);

        $sqlTable = $connection->escapeAndQuoteTable($dbTable);

        $sqlFields  = array();
        $sqlTokens  = array();
        $parameters = array();

        foreach ($row as $columnName => $value) {
            $sqlFields[]  = $connection->escapeAndQuoteField($columnName);
            $sqlTokens[]  = '?';
            $parameters[] = $value;
        }

        $sql = "INSERT INTO {$sqlTable} (" . join(', ', $sqlFields) . ") VALUES (" . join(', ', $sqlTokens) . ")";

        $connection->execute($sql, $parameters);

        $entityMetadata->getPrimaryKey()->bind($connection, $entity);
    }
}
