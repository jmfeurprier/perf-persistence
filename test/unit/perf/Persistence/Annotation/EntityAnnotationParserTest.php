<?php

namespace perf\Persistence\Annotation;

/**
 *
 * @perf\Persistence\Entity(connectionId="foo",table="bar")
 */
class Test_ValidEntity {

    /**
     *
     * @perf\Persistence\Column(name="baz",type="integer")
     * @perf\Persistence\PrimaryKey(strategy="increment")
     */
    private $id;

    /**
     *
     * @perf\Persistence\Column(name="qux",type="text")
     */
    private $content;
}

/**
 *
 */
class EntityAnnotationParserTest extends \PHPUnit_Framework_TestCase
{

    /**
     *
     */
    protected function setUp()
    {
        $this->entityAnnotationParser = new EntityAnnotationParser();
    }

    /**
     *
     */
    public function testWithNonExistentClassWillThrowException()
    {
        $entityClass = '\\unset';

        $this->setExpectedException("\\RuntimeException", "Class '{$entityClass}' not found.");

        $this->entityAnnotationParser->parse($entityClass);
    }

    /**
     *
     */
    public function testWithValidClassWillReturnExpected()
    {
        $entityClass = '\\perf\\Persistence\\Annotation\\Test_ValidEntity';

        $result = $this->entityAnnotationParser->parse($entityClass);

        $this->assertInstanceOf('\\perf\\Persistence\\EntityMetadata', $result);
        $this->assertSame($entityClass, $result->getClass());
        $this->assertSame('foo', $result->getStorage()->getConnectionId());
        $this->assertSame('bar', $result->getStorage()->getTable());

        $resultColumns = $result->getColumns();
        $this->assertCount(2, $resultColumns);

        $resultColumnPrimary = reset($resultColumns);
        $this->assertSame('id', $resultColumnPrimary->getPropertyName());
        $this->assertSame('baz', $resultColumnPrimary->getColumnName());
        $this->assertSame('integer', $resultColumnPrimary->getType());

        $resultColumnSecondary = end($resultColumns);
        $this->assertSame('content', $resultColumnSecondary->getPropertyName());
        $this->assertSame('qux', $resultColumnSecondary->getColumnName());
        $this->assertSame('text', $resultColumnSecondary->getType());

        $resultPrimaryKey = $result->getPrimaryKey();
        $this->assertInstanceOf('\\perf\\Persistence\\IncrementalPrimaryKey', $resultPrimaryKey);

        $resultPrimaryKeyColumns = $resultPrimaryKey->getColumns();
        $this->assertCount(1, $resultPrimaryKeyColumns);

        $resultPrimaryKeyColumn = reset($resultPrimaryKeyColumns);

        $this->assertSame($resultColumnPrimary, $resultPrimaryKeyColumn);
    }
}
