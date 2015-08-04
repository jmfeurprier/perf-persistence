<?php

namespace perf\Persistence;

/**
 *
 *
 */
class NaturalPrimaryKey implements PrimaryKey
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
        if (count($columns) < 1) {
            throw new \InvalidArgumentException('At least one column must be provided.');
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
     * @param \perf\Db\Connection $connection
     * @param object $entity
     * @return \perf\Db\QueryFilter
     */
    public function getFilter(\perf\Db\Connection $connection, $entity)
    {
        $reflectionObject = new \ReflectionObject($entity);

        $clauses    = array();
        $parameters = array();

        foreach ($this->columns as $column) {
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

            $clauses[]    = "({$sqlColumnName} = ?)";
            $parameters[] = $value;
        }

        $clause = join(' AND ', $clauses);

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
        // Nothing to do
    }

    /**
     *
     *
     * @param object $entity
     * @return void
     */
    public function unbind($entity)
    {
        // Nothing to do
    }
}
