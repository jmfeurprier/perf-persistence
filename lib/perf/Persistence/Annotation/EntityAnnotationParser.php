<?php

namespace perf\Persistence\Annotation;

use \perf\Annotation\DocBlockParser;
use \perf\Persistence\Storage;
use \perf\Persistence\Column;
use \perf\Persistence\EntityMetadata;

/**
 *
 *
 */
class EntityAnnotationParser
{

    /**
     *
     *
     * @var DocBlockParser
     */
    private $docBlockAnnotationParser;

    /**
     *
     *
     * @var StorageAnnotationParser
     */
    private $storageAnnotationParser;

    /**
     *
     *
     * @var PropertiesAnnotationsExtractor
     */
    private $propertiesAnnotationsExtractor;

    /**
     *
     *
     * @var PrimaryKeyExtractor
     */
    private $primaryKeyExtractor;

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
     * @var \ReflectionClass
     */
    private $reflectionClass;

    /**
     *
     * Temporary property.
     *
     * @var Storage
     */
    private $storage;

    /**
     *
     * Temporary property.
     *
     * @var Column[]
     */
    private $columns = array();

    /**
     *
     * Temporary property.
     *
     * @var PrimaryKey
     */
    private $primaryKey;

    /**
     * Constructor.
     *
     * @return void
     */
    public function __construct()
    {
        $this->setDockBlockAnnotationParser(new DocBlockParser());
        $this->setStorageAnnotationParser(new StorageAnnotationParser());
        $this->setPropertiesAnnotationsExtractor(new PropertiesAnnotationsExtractor());
        $this->setPrimaryKeyExtractor(new PrimaryKeyExtractor());
    }

    /**
     *
     *
     * @param DocBlockParser $parser
     * @return void
     */
    public function setDockBlockAnnotationParser(DocBlockParser $parser)
    {
        $this->docBlockAnnotationParser = $parser;
    }

    /**
     *
     *
     * @param StorageAnnotationParser $parser
     * @return void
     */
    public function setStorageAnnotationParser(StorageAnnotationParser $parser)
    {
        $this->storageAnnotationParser = $parser;
    }

    /**
     *
     *
     * @param PropertiesAnnotationsExtractor $parser
     * @return void
     */
    public function setPropertiesAnnotationsExtractor(PropertiesAnnotationsExtractor $parser)
    {
        $this->propertiesAnnotationsExtractor = $parser;
    }

    /**
     *
     *
     * @param PrimaryKeyExtractor $extractor
     * @return void
     */
    public function setPrimaryKeyExtractor(PrimaryKeyExtractor $extractor)
    {
        $this->primaryKeyExtractor = $extractor;
    }

    /**
     *
     *
     * @param string $entityClass
     * @return EntityMetadata
     * @throws \RuntimeException
     */
    public function parse($entityClass)
    {
        $this->init($entityClass);

        $this->parseStorageAnnotations();
        $this->readPropertiesAnnotations();
        $this->extractColumns();
        $this->extractPrimaryKey();

        return $this->conclude();
    }

    /**
     *
     *
     * @param string $entityClass
     * @return void
     * @throws \RuntimeException
     */
    private function init($entityClass)
    {
        if (!class_exists($entityClass)) {
            throw new \RuntimeException("Class '{$entityClass}' not found.");
        }

        $this->entityClass     = $entityClass;
        $this->reflectionClass = new \ReflectionClass($this->entityClass);
        $this->storage         = null;
        $this->columns         = array();
        $this->primaryKey      = null;
    }

    /**
     *
     *
     * @return void
     * @throws \RuntimeException
     */
    private function parseStorageAnnotations()
    {
        try {
            $this->storage = $this->storageAnnotationParser->parse($this->reflectionClass);
        } catch (\Exception $e) {
            throw new \RuntimeException("Failed to parse storage annotations.", 0, $e);
        }
    }

    /**
     *
     *
     * @return void
     * @throws \RuntimeException
     */
    private function readPropertiesAnnotations()
    {
        $this->propertiesAnnotations = $this->propertiesAnnotationsExtractor->extract($this->reflectionClass);
    }

    /**
     *
     *
     * @return void
     * @throws \RuntimeException
     */
    private function extractColumns()
    {
        foreach ($this->propertiesAnnotations as $propertyName => $propertyAnnotations) {
            $this->extractColumn($propertyName, $propertyAnnotations);
        }
    }

    /**
     *
     *
     * @param string $propertyName
     * @param Annotation[] $propertyAnnotations
     * @return void
     * @throws \RuntimeException
     */
    private function extractColumn($propertyName, array $propertyAnnotations)
    {
        $columnAnnotations = array();

        foreach ($propertyAnnotations as $annotation) {
            if ('perf\Persistence\Column' === $annotation->getKey()) {
                $columnAnnotations[] = $annotation;
            }
        }

        if (count($columnAnnotations) < 1) {
            return;
        }

        if (count($columnAnnotations) > 1) {
            throw new \RuntimeException('Doc block contains more than one column annotation.');
        }

        $columnAnnotation = reset($columnAnnotations);

        if ($columnAnnotation->hasParameter('name')) {
            $columnName = $columnAnnotation->getParameter('name');
        } else {
            $columnName = $propertyName;
        }

        $type = $columnAnnotation->getParameter('type');

        $column = new Column($propertyName, $columnName, $type);

        $this->columns[] = $column;
    }

    /**
     *
     *
     * @return void
     * @throws \RuntimeException
     */
    private function extractPrimaryKey()
    {
        $this->primaryKey = $this->primaryKeyExtractor->extract($this->columns, $this->propertiesAnnotations);
    }

    /**
     *
     *
     * @return EntityMetadata
     */
    private function conclude()
    {
        $entityMetadata = new EntityMetadata($this->entityClass, $this->storage, $this->columns, $this->primaryKey);

        $this->entityClass     = null;
        $this->reflectionClass = null;
        $this->storage         = null;
        $this->columns         = array();
        $this->primaryKey      = null;

        return $entityMetadata;
    }
}
