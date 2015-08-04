<?php

namespace perf\Persistence;

/**
 *
 *
 */
class EntityMetadata
{

    /**
     *
     *
     * @var string
     */
    private $class;

    /**
     *
     *
     * @var Storage
     */
    private $storage;

    /**
     *
     *
     * @var Column[]
     */
    private $columns;

    /**
     *
     *
     * @var PrimaryKey
     */
    private $primaryKey;

    /**
     * Constructor.
     *
     * @param string $class
     * @param Storage $storage
     * @param Column[] $columns
     * @param PrimaryKey $primaryKey
     * @return void
     */
    public function __construct($class, Storage $storage, array $columns, PrimaryKey $primaryKey)
    {
        if (!is_string($class)) {
            throw new \InvalidArgumentException('Provided entity class is not a string.');
        }

        if (count($columns) < 1) {
            throw new \InvalidArgumentException('No column provided.');
        }

        $this->class      = $class;
        $this->storage    = $storage;
        $this->columns    = $columns;
        $this->primaryKey = $primaryKey;
    }

    /**
     *
     *
     * @return string
     */
    public function getClass()
    {
        return $this->class;
    }

    /**
     *
     *
     * @return Storage
     */
    public function getStorage()
    {
        return $this->storage;
    }

    /**
     *
     *
     * @return Column[]
     */
    public function getColumns()
    {
        return $this->columns;
    }

    /**
     *
     *
     * @return PrimaryKey
     */
    public function getPrimaryKey()
    {
        return $this->primaryKey;
    }
}
