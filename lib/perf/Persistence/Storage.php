<?php

namespace perf\Persistence;

/**
 *
 *
 */
class Storage
{

    /**
     *
     *
     * @var string
     */
    private $connectionId;

    /**
     *
     *
     * @var string
     */
    private $table;

    /**
     * Constructor.
     *
     * @param string $connectionId
     * @param string $table
     * @return void
     */
    public function __construct($connectionId, $table)
    {
        $this->connectionId = (string) $connectionId;
        $this->table        = (string) $table;
    }

    /**
     *
     *
     * @return string
     */
    public function getConnectionId()
    {
        return $this->connectionId;
    }

    /**
     *
     *
     * @return string
     */
    public function getTable()
    {
        return $this->table;
    }
}
