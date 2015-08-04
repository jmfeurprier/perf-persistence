<?php

namespace perf\Persistence\Annotation;

/**
 *
 *
 */
class PropertiesAnnotationsExtractor
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
     * @return {string:\perf\Annotation\Annotation[]}
     * @throws \RuntimeException
     */
    public function extract(\ReflectionClass $reflectionClass)
    {
        $propertiesFilter = \ReflectionProperty::IS_PUBLIC
                          | \ReflectionProperty::IS_PROTECTED
                          | \ReflectionProperty::IS_PRIVATE;

        $annotations = array();

        foreach ($reflectionClass->getProperties($propertiesFilter) as $reflectionProperty) {
            $docComment = $reflectionProperty->getDocComment();

            if (false === $docComment) {
                continue;
            }

            $propertyName = $reflectionProperty->getName();

            $annotations[$propertyName] = $this->docBlockAnnotationParser->parse($docComment);
        }

        return $annotations;
    }
}
