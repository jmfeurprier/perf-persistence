<?php

namespace perf\Persistence\Operation;

use \perf\Persistence\EntityMetadata;

/**
 *
 *
 */
class EntityExporter
{

    /**
     *
     * Temporary property.
     *
     * @var object
     */
    private $entity;

    /**
     *
     * Temporary property.
     *
     * @var \ReflectionClass
     */
    private $reflectionClass;

    /**
     *
     *
     * @param EntityMetadata $entityMetadata
     * @param object $entity
     * @return {string:mixed}
     */
    public function export(EntityMetadata $entityMetadata, $entity)
    {
        $entityClass = $entityMetadata->getClass();

        $this->entity = $entity;

        $this->loadReflectionClass($entityClass);

        $row = array();

        foreach ($entityMetadata->getColumns() as $entityColumn) {
            $propertyName = $entityColumn->getPropertyName();
            $columnName   = $entityColumn->getColumnName();

            $value = $this->getPropertyValue($propertyName);

            $row[$columnName] = $value;
        }

        return $row;
    }

    /**
     *
     *
     * @param string $entityClass
     * @return void
     */
    private function loadReflectionClass($entityClass)
    {
        static $cache = array();

        if (!array_key_exists($entityClass, $cache)) {
            $cache[$entityClass] = new \ReflectionClass($entityClass);
        }

        $this->reflectionClass = $cache[$entityClass];
    }

    /**
     *
     *
     * @param string $propertyName
     * @return null|string
     */
    private function getPropertyValue($propertyName)
    {
        $reflectionProperty = $this->reflectionClass->getProperty($propertyName);

        if ($reflectionProperty->isPublic()) {
            $value = $reflectionProperty->getValue($this->entity);
        } else {
            $reflectionProperty->setAccessible(true);
            $value = $reflectionProperty->getValue($this->entity);
            $reflectionProperty->setAccessible(false);
        }

        if (!is_null($value)) {
            if (is_bool($value)) {
                $value = (int) $value;
            }

            $value = (string) $value;
        }

        return $value;
    }
}
