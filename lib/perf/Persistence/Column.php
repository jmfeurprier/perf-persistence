<?php

namespace perf\Persistence;

/**
 *
 *
 */
class Column
{

    /**
     *
     *
     * @var string
     */
    private $propertyName;

    /**
     *
     *
     * @var string
     */
    private $columnName;

    /**
     *
     *
     * @var string
     */
    private $type;

    /**
     * Constructor.
     *
     * @param string $propertyName
     * @param string $columnName
     * @param string $type
     * @return void
     */
    public function __construct($propertyName, $columnName, $type)
    {
        $this->propertyName = (string) $propertyName;
        $this->columnName   = (string) $columnName;
        $this->type         = (string) $type;
    }

    /**
     *
     *
     * @return string
     */
    public function getPropertyName()
    {
        return $this->propertyName;
    }

    /**
     *
     *
     * @return string
     */
    public function getColumnName()
    {
        return $this->columnName;
    }

    /**
     *
     *
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }
}
