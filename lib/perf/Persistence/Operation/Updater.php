<?php

namespace perf\Persistence\Operation;

use \perf\Persistence\EntityMetadataPool;
use \perf\Persistence\EntityMetadata;
use \perf\Db\ConnectionPool;
use \perf\Db\Connection;

/**
 *
 *
 */
class Updater extends Operator
{

    /**
     *
     *
     * @var EntityExporter
     */
    private $entityExporter;

    /**
     *
     * Temporary property.
     *
     * @var EntityMetadata
     */
    private $entityMetadata;

    /**
     *
     * Temporary property.
     *
     * @var Connection
     */
    private $connection;

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
     * Updates provided entity within storage.
     *
     * @param object $entity
     * @return void
     * @throws \DomainException
     * @throws \RuntimeException
     */
    public function update($entity)
    {
        $this->entityMetadata = $this->getEntityMetadata($entity);
        $entityStorage        = $this->entityMetadata->getStorage();
        $connectionId         = $entityStorage->getConnectionId();
        $this->connection     = $this->getConnection($connectionId);
        $dbTable              = $entityStorage->getTable();

        $sqlTable = $this->connection->escapeAndQuoteTable($dbTable);

        $sqlProperties = array();
        $parameters    = array();

        foreach ($this->exportEntity($entity) as $columnName => $value) {
            $sqlField        = $this->connection->escapeAndQuoteField($columnName);
            $sqlProperties[] = "{$sqlField} = ?";
            $parameters[]    = $value;
        }

        $primaryKeyFilter = $this->getPrimaryKeyFilter($entity);

        $sqlWhere = $primaryKeyFilter->getClause();

        foreach ($primaryKeyFilter->getParameters() as $parameter) {
            $parameters[] = $parameter;
        }

        $sql = "UPDATE {$sqlTable} SET " . join(', ', $sqlProperties) . " WHERE {$sqlWhere}";

        $this->connection->execute($sql, $parameters);
    }

    /**
     *
     *
     * @param object $entity
     * @return {string:mixed}
     */
    private function exportEntity($entity)
    {
        $row = $this->entityExporter->export($this->entityMetadata, $entity);

        // Remove primary key columns from updatable columns
        foreach ($this->entityMetadata->getPrimaryKey()->getColumns() as $column) {
            $columnName = $column->getColumnName();

            unset($row[$columnName]);
        }

        if (count($row) < 1) {
            throw new \RuntimeException('Nothing to update.');
        }

        return $row;
    }

    /**
     *
     *
     * @param object $entity
     * @return \perf\Db\QueryFilter
     */
    private function getPrimaryKeyFilter($entity)
    {
        return $this->entityMetadata->getPrimaryKey()->getFilter($this->connection, $entity);
    }
}
