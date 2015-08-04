<?php

namespace perf\Persistence\Annotation;

/**
 *
 */
class StorageAnnotationParserTest extends \PHPUnit_Framework_TestCase
{

    /**
     *
     */
    protected function setUp()
    {
        $this->storageAnnotationParser = new StorageAnnotationParser();
    }

    /**
     *
     */
    public function testWithoutDocBlockWillThrowException()
    {
        $reflectionClass = $this->getMockBuilder('\\ReflectionClass')->disableOriginalConstructor()->getMock();
        $reflectionClass->expects($this->atLeastOnce())->method('getDocComment')->will($this->returnValue(false));

        $this->setExpectedException("\\RuntimeException", "Doc block is missing.");

        $this->storageAnnotationParser->parse($reflectionClass);
    }

    /**
     *
     */
    public function testWithoutAnnotationWillThrowException()
    {
        $docComment = <<<DOC
/**
 *
 */
DOC;

        $reflectionClass = $this->getMockBuilder('\\ReflectionClass')->disableOriginalConstructor()->getMock();
        $reflectionClass->expects($this->atLeastOnce())->method('getDocComment')->will($this->returnValue($docComment));

        $this->setExpectedException("\\RuntimeException", "Doc block does not contain annotations.");

        $this->storageAnnotationParser->parse($reflectionClass);
    }

    /**
     *
     */
    public function testWithoutPersistenceAnnotationWillThrowException()
    {
        $docComment = <<<DOC
/**
 * @foo
 */
DOC;

        $reflectionClass = $this->getMockBuilder('\\ReflectionClass')->disableOriginalConstructor()->getMock();
        $reflectionClass->expects($this->atLeastOnce())->method('getDocComment')->will($this->returnValue($docComment));

        $this->setExpectedException("\\RuntimeException", "Doc block does not contain persistence annotation.");

        $this->storageAnnotationParser->parse($reflectionClass);
    }

    /**
     *
     */
    public function testWithMultiplePersistenceAnnotationsWillThrowException()
    {
        $docComment = <<<DOC
/**
 * @perf\Persistence\Entity(connectionId="bar",table="baz")
 * @perf\Persistence\Entity(connectionId="bar",table="baz")
 */
DOC;

        $reflectionClass = $this->getMockBuilder('\\ReflectionClass')->disableOriginalConstructor()->getMock();
        $reflectionClass->expects($this->atLeastOnce())->method('getDocComment')->will($this->returnValue($docComment));

        $this->setExpectedException("\\RuntimeException", "Doc block contains more than one persistence annotation.");

        $this->storageAnnotationParser->parse($reflectionClass);
    }

    /**
     *
     */
    public function testWithClassWithValidPersistenceAnnotationsWillReturnExpected()
    {
        $class      = 'foo';
        $docComment = <<<DOC
/**
 * @perf\Persistence\Entity(connectionId="bar",table="baz")
 */
DOC;

        $reflectionClass = $this->getMockBuilder('\\ReflectionClass')->disableOriginalConstructor()->getMock();
        #$reflectionClass->expects($this->atLeastOnce())->method('getName')->will($this->returnValue($class));
        $reflectionClass->expects($this->atLeastOnce())->method('getDocComment')->will($this->returnValue($docComment));

        $result = $this->storageAnnotationParser->parse($reflectionClass);

        $this->assertInstanceOf('\\perf\\Persistence\\Storage', $result);
        $this->assertSame('bar', $result->getConnectionId());
        $this->assertSame('baz', $result->getTable());
    }
}
