<?php

namespace perf\Persistence\Operation;

use \perf\Persistence\EntityMetadataPool;
use \perf\Db\ConnectionPool;
use \perf\Db\Connection;
use \perf\Persistence\EntityIterator;

/**
 *
 *
 */
class Selecter extends Operator
{

    /**
     * Entity importer.
     *
     * @var EntityImporter
     */
    private $entityImporter;

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
     * Temporary property.
     *
     * @var string[]
     */
    private $sqlChunks = array();

    /**
     *
     * Temporary property.
     *
     * @var array
     */
    private $parameters = array();

    /**
     * Sets entity importer.
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
     * @param string $entityClass
     * @param null|\perf\Db\QueryFilter $filter
     * @param null|\perf\Db\QuerySorting $sorting
     * @param int $offset
     * @param null|int $limit
     * @return EntityIterator
     * @throws \InvalidArgumentException
     */
    public function select(
        $entityClass,
        \perf\Db\QueryFilter $filter = null,
        \perf\Db\QuerySorting $sorting = null,
        $offset = 0,
        $limit = null
    ) {
        $this->init($entityClass);

        $this->columns();
        $this->from();
        $this->where($filter);
        $this->orderBy($sorting);
        $this->limit($offset, $limit);

        return $this->conclude();
    }

    /**
     *
     *
     * @param string $entityClass
     * @return void
     */
    private function init($entityClass)
    {
        $this->entityMetadata = $this->getEntityClassMetadata($entityClass);
        $entityStorage        = $this->entityMetadata->getStorage();
        $connectionId         = $entityStorage->getConnectionId();
        $this->connection     = $this->getConnection($connectionId);
        $this->sqlChunks      = array();
        $this->parameters     = array();
    }

    /**
     *
     *
     * @return void
     */
    private function columns()
    {
        $sqlColumns = array();

        foreach ($this->entityMetadata->getColumns() as $column) {
            $sqlColumns[] = $this->connection->escapeAndQuoteField($column->getColumnName());
        }

        $this->sqlChunks[] = 'SELECT ' . join(', ', $sqlColumns);
    }

    /**
     *
     *
     * @return void
     */
    private function from()
    {
        $entityStorage = $this->entityMetadata->getStorage();
        $table         = $entityStorage->getTable();

        $this->sqlChunks[] = 'FROM ' . $this->connection->escapeAndQuoteTable($table);
    }

    /**
     *
     *
     * @param null|\perf\Db\QueryFilter $filter
     * @return void
     */
    private function where(\perf\Db\QueryFilter $filter = null)
    {
        if (null !== $filter) {
            $this->sqlChunks[]  = 'WHERE ' . $filter->getClause();

            foreach ($filter->getParameters() as $parameter) {
                $this->parameters[] = $parameter;
            }
        }
    }

    /**
     *
     *
     * @param null|\perf\Db\QuerySorting $sorting
     * @return void
     */
    private function orderBy(\perf\Db\QuerySorting $sorting = null)
    {
        if (null !== $sorting) {
            $this->sqlChunks[] = 'ORDER BY ' . $sorting->getClause();
        }
    }

    /**
     *
     *
     * @param int $offset
     * @param null|int $limit
     * @return void
     * @throws \InvalidArgumentException
     */
    private function limit($offset, $limit)
    {
        $this->validateLimit($offset, $limit);

        $hasOffset = ($offset > 0);

        if (null === $limit) {
            if (!$hasOffset) {
                return;
            }

            $limit = PHP_INT_MAX;
        }

        $this->sqlChunks[] = "LIMIT {$limit}";

        if ($hasOffset) {
            $this->sqlChunks[] = "OFFSET {$offset}";
        }
    }

    /**
     *
     *
     * @param int $offset
     * @param null|int $limit
     * @return void
     * @throws \InvalidArgumentException
     */
    private function validateLimit($offset, $limit)
    {
        if (!is_int($offset)) {
            throw new \InvalidArgumentException('Offset must be an integer.');
        }

        if ($offset < 0) {
            throw new \InvalidArgumentException('Offset must be equal to or greater than zero.');
        }

        if (is_int($limit)) {
            if ($limit < 1) {
                throw new \InvalidArgumentException('Limit must be greater than zero.');
            }
        } elseif (null !== $limit) {
            throw new \InvalidArgumentException('Limit must be an integer or null.');
        }
    }

    /**
     *
     *
     * @return EntityIterator
     * @throws \RuntimeException
     */
    private function conclude()
    {
        $sql = join(' ', $this->sqlChunks);

        $result = $this->connection->query($sql, $this->parameters);

        return new EntityIterator($this->entityImporter, $this->entityMetadata, $result);
    }
}
