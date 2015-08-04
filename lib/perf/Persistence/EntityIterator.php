<?php

namespace perf\Persistence;

use \perf\Db\QueryResult;
use \perf\Persistence\Operation\EntityImporter;

/**
 *
 *
 * @internal
 */
class EntityIterator implements \Iterator
{

    /**
     *
     *
     * @var EntityImporter
     */
    private $entityImporter;

    /**
     *
     *
     * @var EntityMetadata
     */
    private $entityMetadata;

    /**
     *
     *
     * @var QueryResult
     */
    private $result;

    /**
     *
     *
     * @var null|Entity
     */
    private $current;

    /**
     * Constructor.
     *
     * @param EntityImporter $entityImporter
     * @param EntityMetadata $entityMetadata
     * @param QueryResult $result
     * @return void
     */
    public function __construct(EntityImporter $entityImporter, EntityMetadata $entityMetadata, QueryResult $result)
    {
        $this->entityImporter = $entityImporter;
        $this->entityMetadata = $entityMetadata;
        $this->result         = $result;
    }

    /**
     *
     *
     * @return int
     */
    public function key()
    {
        return $this->result->key();
    }

    /**
     *
     *
     * @return Entity
     */
    public function current()
    {
        if (is_null($this->current)) {
            $entity = $this->entityImporter->import($this->entityMetadata, $this->result->current());

            $this->current = $entity;
        }

        return $this->current;
    }

    /**
     *
     *
     * @return void
     */
    public function next()
    {
        $this->current = null;

        $this->result->next();
    }

    /**
     *
     *
     * @return void
     */
    public function rewind()
    {
        $this->current = null;

        $this->result->rewind();
    }

    /**
     *
     *
     * @return bool
     */
    public function valid()
    {
        return $this->result->valid();
    }
}
