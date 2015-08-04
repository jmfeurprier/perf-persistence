<?php

namespace perf\Persistence\Annotation;

use \perf\Persistence\Storage;

/**
 *
 *
 */
class StorageAnnotationParser
{

    /**
     *
     *
     * @var \perf\Annotation\DocBlockParser
     */
    private $docBlockAnnotationParser;

    /**
     * Constructor.
     *
     * @return void
     */
    public function __construct()
    {
        $this->setDockBlockAnnotationParser(new \perf\Annotation\DocBlockParser());
    }

    /**
     *
     *
     * @param \perf\Annotation\DocBlockParser $parser
     * @return void
     */
    public function setDockBlockAnnotationParser(\perf\Annotation\DocBlockParser $parser)
    {
        $this->docBlockAnnotationParser = $parser;
    }

    /**
     *
     *
     * @param \ReflectionClass $reflectionClass
     * @return Storage
     * @throws \RuntimeException
     */
    public function parse(\ReflectionClass $reflectionClass)
    {
        $persistenceAnnotation = $this->getPersistenceAnnotation($reflectionClass);

        $connectionId = $persistenceAnnotation->getParameter('connectionId');
        $table        = $persistenceAnnotation->getParameter('table');

        return new Storage($connectionId, $table);
    }

    /**
     *
     *
     * @param \ReflectionClass $reflectionClass
     * @return Annotation
     * @throws \RuntimeException
     */
    private function getPersistenceAnnotation(\ReflectionClass $reflectionClass)
    {
        $persistenceAnnotations = array();

        foreach ($this->getAllAnnotations($reflectionClass) as $annotation) {
            if ('perf\\Persistence\\Entity' === $annotation->getKey()) {
                $persistenceAnnotations[] = $annotation;
            }
        }

        if (count($persistenceAnnotations) < 1) {
            throw new \RuntimeException("Doc block does not contain persistence annotation.");
        }

        if (count($persistenceAnnotations) > 1) {
            throw new \RuntimeException("Doc block contains more than one persistence annotation.");
        }

        $persistenceAnnotation = reset($persistenceAnnotations);

        return $persistenceAnnotation;
    }

    /**
     *
     *
     * @param \ReflectionClass $reflectionClass
     * @return Annotation[]
     * @throws \RuntimeException
     */
    private function getAllAnnotations(\ReflectionClass $reflectionClass)
    {
        $docComment = $reflectionClass->getDocComment();

        if (false == $docComment) {
            throw new \RuntimeException("Doc block is missing.");
        }

        $annotations = $this->docBlockAnnotationParser->parse($docComment);

        if (count($annotations) < 1) {
            throw new \RuntimeException("Doc block does not contain annotations.");
        }

        return $annotations;
    }
}
