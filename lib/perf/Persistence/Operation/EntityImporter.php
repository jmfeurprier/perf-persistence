<?php

namespace perf\Persistence\Operation;

use \perf\Persistence\EntityMetadata;
use \perf\Persistence\Column;

/**
 *
 *
 */
class EntityImporter
{

    /**
     *
     *
     * @var {string:\ReflectionClass}
     */
    private $reflectionClassesCache = array();

    /**
     *
     *
     * @var {string:{string:\ReflectionProperty}}
     */
    private $reflectionPropertiesCache = array();

    /**
     *
     * Temporary property.
     *
     * @var string
     */
    private $entityClass;

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
     * @param {string:mixed} $row
     * @return object
     */
    public function import(EntityMetadata $entityMetadata, array $row)
    {
        $this->entityClass = $entityMetadata->getClass();
        $this->entity      = new $this->entityClass();

        $this->loadReflectionClass();

        foreach ($entityMetadata->getColumns() as $column) {
            $columnName = $column->getColumnName();

            if (array_key_exists($columnName, $row)) {
                $propertyName = $column->getPropertyName();

                $value = $row[$columnName];
                $value = $this->setValueType($value, $column);

                $this->getReflectionProperty($propertyName)->setValue($this->entity, $value);
            }
        }

        return $this->entity;
    }

    /**
     *
     *
     * @return void
     */
    private function loadReflectionClass()
    {
        if (!array_key_exists($this->entityClass, $this->reflectionClassesCache)) {
            $reflectionClass = new \ReflectionClass($this->entityClass);

            $this->reflectionClassesCache[$this->entityClass] = $reflectionClass;
        }

        $this->reflectionClass = $this->reflectionClassesCache[$this->entityClass];
    }

    /**
     *
     *
     * @param null|string $value
     * @param Column $column
     * @return mixed
     */
    private function setValueType($value, Column $column)
    {
        if (null === $value) {
            return null;
        }

        switch ($column->getType()) {
            case 'int':
            case 'integer':
                return (int) $value;

            case 'float':
            case 'double':
                return (float) $value;

            case 'bool':
            case 'boolean':
                return (bool) $value;
        }

        return $value;
    }

    /**
     *
     *
     * @param string $propertyName
     * @return \ReflectionProperty
     */
    private function getReflectionProperty($propertyName)
    {
        if (isset($this->reflectionPropertiesCache[$this->entityClass][$propertyName])) {
            return $this->reflectionPropertiesCache[$this->entityClass][$propertyName];
        }

        $reflectionProperty = $this->reflectionClass->getProperty($propertyName);

        if (!$reflectionProperty->isPublic()) {
            $reflectionProperty->setAccessible(true);
        }

        $this->reflectionPropertiesCache[$this->entityClass][$propertyName] = $reflectionProperty;

        return $reflectionProperty;
    }
}
