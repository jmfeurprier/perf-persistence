<?php

namespace perf\Persistence\Annotation;

use \perf\Annotation\Annotation;
use \perf\Persistence\Column;
use \perf\Persistence\IncrementalPrimaryKey;
use \perf\Persistence\NaturalPrimaryKey;

/**
 *
 *
 */
class PrimaryKeyExtractor
{

    /**
     *
     * Temporary property.
     *
     * @var {string:Column}
     */
    private $columns = array();

    /**
     *
     * Temporary property.
     *
     * @var {string:Annotation}
     */
    private $propertiesAnnotations = array();

    /**
     *
     * Temporary property.
     *
     * @var {string:Annotation[]}
     */
    private $primaryKeyAnnotations = array();

    /**
     *
     * Temporary property.
     *
     * @var Column[]
     */
    private $primaryKeyColumns = array();

    /**
     *
     * Temporary property.
     *
     * @var string
     */
    private $strategy;

    /**
     *
     *
     * @param Column[] $columns
     * @param {string:Annotation[]} $propertiesAnnotations
     * @return PrimaryKey
     * @throws \RuntimeException
     */
    public function extract(array $columns, array $propertiesAnnotations)
    {
        $this->init($columns, $propertiesAnnotations);

        $this->extractPrimaryKeyAnnotations();
        $this->extractPrimaryKeyColumns();
        $this->extractStrategy();

        return $this->conclude();
    }

    /**
     *
     *
     * @param Column[] $columns
     * @param {string:Annotation[]} $propertiesAnnotations
     * @return void
     */
    private function init(array $columns, array $propertiesAnnotations)
    {
        foreach ($columns as $column) {
            $propertyName = $column->getPropertyName();

            $this->columns[$propertyName] = $column;
        }

        $this->propertiesAnnotations = $propertiesAnnotations;
        $this->primaryKeyAnnotations = array();
        $this->primaryKeyColumns     = array();
        $this->strategy              = null;
    }

    /**
     *
     *
     * @return void
     * @throws \RuntimeException
     */
    private function extractPrimaryKeyAnnotations()
    {
        $primaryKeyAnnotations = array();

        foreach ($this->propertiesAnnotations as $propertyName => $propertyAnnotations) {
            $propertyPrimaryKeyAnnotations = array();

            foreach ($propertyAnnotations as $annotation) {
                if ('perf\\Persistence\\PrimaryKey' === $annotation->getKey()) {
                    $propertyPrimaryKeyAnnotations[] = $annotation;
                }
            }

            $propertyPrimaryKeyAnnotationCount = count($propertyPrimaryKeyAnnotations);

            if ($propertyPrimaryKeyAnnotationCount < 1) {
                continue;
            }

            if ($propertyPrimaryKeyAnnotationCount > 1) {
                $message = "More than one primary key annotation found for property '{$propertyName}'.";

                throw new \RuntimeException($message);
            }

            $propertyPrimaryKeyAnnotation = reset($propertyPrimaryKeyAnnotations);

            $primaryKeyAnnotations[$propertyName] = $propertyPrimaryKeyAnnotation;
        }

        if (count($primaryKeyAnnotations) < 1) {
            throw new \RuntimeException('No primary key annotation found.');
        }

        $this->primaryKeyAnnotations = $primaryKeyAnnotations;
    }

    /**
     *
     *
     * @return void
     * @throws \RuntimeException
     */
    private function extractPrimaryKeyColumns()
    {
        $primaryKeyColumns = array();

        foreach (array_keys($this->primaryKeyAnnotations) as $propertyName) {
            if (!array_key_exists($propertyName, $this->columns)) {
                throw new \RuntimeException("Primary key column '{$propertyName}' not found.");
            }

            $primaryKeyColumns[] = $this->columns[$propertyName];
        }

        $this->primaryKeyColumns = $primaryKeyColumns;
    }

    /**
     *
     *
     * @return void
     * @throws \RuntimeException
     */
    private function extractStrategy()
    {
        $strategies = array();

        foreach ($this->primaryKeyAnnotations as $primaryKeyAnnotation) {
            $strategy = $primaryKeyAnnotation->getParameter('strategy');

            $strategies[$strategy] = $strategy;
        }

        $strategyCount = count($strategies);

        if ($strategyCount < 1) {
            throw new \RuntimeException("No primary key strategy defined.");
        }

        if ($strategyCount > 1) {
            throw new \RuntimeException("More than one primary key strategy defined.");
        }

        $strategy = reset($strategies);

        $this->strategy = $strategy;
    }

    /**
     *
     *
     * @return PrimaryKey
     * @throws \RuntimeException
     */
    private function conclude()
    {
        switch ($this->strategy) {
            case 'natural':
                return new NaturalPrimaryKey($this->primaryKeyColumns);
            case 'increment':
                return new IncrementalPrimaryKey($this->primaryKeyColumns);
            default:
                throw new \RuntimeException("Unknown primary key strategy '{$this->strategy}'.");
        }
    }
}
