<?php

namespace perf\Persistence;

/**
 *
 *
 */
class IncrementalPrimaryKey implements PrimaryKey
{

    /**
     *
     *
     * @var Column[]
     */
    private $columns;

    /**
     * Constructor.
     *
     * @param Column[] $columns
     * @return void
     */
    public function __construct(array $columns)
    {
        if (1 !== count($columns)) {
            throw new \InvalidArgumentException('Exactly one column must be provided.');
        }

        foreach ($columns as $column) {
            $this->addColumn($column);
        }
    }

    /**
     *
     *
     * @param Column[] $columns
     * @return void
     */
    private function addColumn(Column $column)
    {
        $this->columns[] = $column;
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
     * @return Column
     */
    private function getColumn()
    {
        return reset($this->columns);
    }

    /**
     *
     *
     * @param \perf\Db\Connection $connection
     * @param object $entity
     * @return \perf\Db\QueryFilter
     */
    public function getFilter(\perf\Db\Connection $connection, $entity)
    {
        $reflectionObject = new \ReflectionObject($entity);

        $column       = $this->getColumn();
        $columnName   = $column->getColumnName();
        $propertyName = $column->getPropertyName();

        $reflectionProperty = $reflectionObject->getProperty($propertyName);

        if ($reflectionProperty->isPublic()) {
            $value = $reflectionProperty->getValue($entity);
        } else {
            $reflectionProperty->setAccessible(true);
            $value = $reflectionProperty->getValue($entity);
            $reflectionProperty->setAccessible(false);
        }

        $sqlColumnName = $connection->escapeAndQuoteField($columnName);

        $clause     = "({$sqlColumnName} = ?)";
        $parameters = array(
            $value,
        );

        return new \perf\Db\QueryFilter($clause, $parameters);
    }

    /**
     *
     *
     * @param \perf\Db\Connection $connection
     * @param object $entity
     * @return void
     */
    public function bind(\perf\Db\Connection $connection, $entity)
    {
        $id = $connection->getInsertId();

        $column = $this->getColumn();

        $reflectionObject = new \ReflectionObject($entity);

        $propertyName = $column->getPropertyName();

        $reflectionProperty = $reflectionObject->getProperty($propertyName);

        if ($reflectionProperty->isPublic()) {
            $reflectionProperty->setValue($entity, $id);
        } else {
            $reflectionProperty->setAccessible(true);
            $reflectionProperty->setValue($entity, $id);
            $reflectionProperty->setAccessible(false);
        }
    }

    /**
     *
     *
     * @param object $entity
     * @return void
     */
    public function unbind($entity)
    {
        $reflectionObject = new \ReflectionObject($entity);

        $column = $this->getColumn();

        $propertyName = $column->getPropertyName();

        $reflectionProperty = $reflectionObject->getProperty($propertyName);

        if ($reflectionProperty->isPublic()) {
            $reflectionProperty->setValue($entity, null);
        } else {
            $reflectionProperty->setAccessible(true);
            $reflectionProperty->setValue($entity, null);
            $reflectionProperty->setAccessible(false);
        }
    }
}
