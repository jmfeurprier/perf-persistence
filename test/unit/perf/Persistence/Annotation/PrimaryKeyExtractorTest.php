<?php

namespace perf\Persistence\Annotation;

/**
 *
 */
class PrimaryKeyExtractorTest extends \PHPUnit_Framework_TestCase
{

    /**
     *
     */
    protected function setUp()
    {
        $this->primaryKeyExtractor = new PrimaryKeyExtractor();
    }

    /**
     *
     */
    public function testWithoutColumnWillThrowException()
    {
        $columns = array();

        $annotation = $this->getMockBuilder('\\perf\\Annotation\\Annotation')->disableOriginalConstructor()->getMock();

        $propertiesAnnotations = array(
            $annotation,
        );

        $this->setExpectedException("\\RuntimeException");

        $this->primaryKeyExtractor->extract($columns, $propertiesAnnotations);
    }

    /**
     *
     */
    public function testWithoutPropertiesAnnotationsWillThrowException()
    {
        $column = $this->getMockBuilder('\\perf\\Persistence\\Column')->disableOriginalConstructor()->getMock();

        $columns = array(
            $column,
        );

        $propertiesAnnotations = array();

        $this->setExpectedException("\\RuntimeException");

        $this->primaryKeyExtractor->extract($columns, $propertiesAnnotations);
    }
}
