<?php

namespace perf\Persistence\Operation;

/**
 *
 */
class Test_Entity
{

    public $publicProperty;

    protected $protectedProperty;

    private $privateProperty;

    public function __construct($publicProperty, $protectedProperty, $privateProperty)
    {
        $this->publicProperty    = $publicProperty;
        $this->protectedProperty = $protectedProperty;
        $this->privateProperty   = $privateProperty;
    }
}

/**
 *
 */
class EntityExporterTest extends \PHPUnit_Framework_TestCase
{

    private $entityClass = '\\perf\\Persistence\\Operation\\Test_Entity';

    /**
     *
     */
    public function testExport()
    {
        $propertyNamePrimary    = 'publicProperty';
        $propertyValuePrimary   = 123;
        $columnNamePrimary      = 'foo';
        $propertyNameSecondary  = 'protectedProperty';
        $propertyValueSecondary = true;
        $columnNameSecondary    = 'bar';
        $propertyNameTertiary   = 'privateProperty';
        $propertyValueTertiary  = 'abc';
        $columnNameTertiary     = 'baz';

        $entity = new Test_Entity($propertyValuePrimary, $propertyValueSecondary, $propertyValueTertiary);

        $columnNames = array(
            $propertyNamePrimary   => $columnNamePrimary,
            $propertyNameSecondary => $columnNameSecondary,
            $propertyNameTertiary  => $columnNameTertiary,
        );

        $this->buildEntityMetadata($columnNames);

        $this->entityExporter = new EntityExporter();

        $result = $this->entityExporter->export($this->entityMetadata, $entity);

        $this->assertInternalType('array', $result);
        $this->assertCount(3, $result);
        $this->assertArrayHasKey($columnNamePrimary, $result);
        $this->assertInternalType('string', $result[$columnNamePrimary]);
        $this->assertEquals($propertyValuePrimary, $result[$columnNamePrimary]);
        $this->assertArrayHasKey($columnNameSecondary, $result);
        $this->assertInternalType('string', $result[$columnNameSecondary]);
        $this->assertEquals($propertyValueSecondary, $result[$columnNameSecondary]);
        $this->assertArrayHasKey($columnNameTertiary, $result);
        $this->assertSame($propertyValueTertiary, $result[$columnNameTertiary]);
    }

    /**
     *
     */
    private function buildEntityMetadata(array $columnNames)
    {
        $columns = array();

        foreach ($columnNames as $propertyName => $columnName) {
            $column = $this->getMockBuilder('\\perf\\Persistence\\Column')->disableOriginalConstructor()->getMock();
            $column->expects($this->atLeastOnce())->method('getPropertyName')->will($this->returnValue($propertyName));
            $column->expects($this->atLeastOnce())->method('getColumnName')->will($this->returnValue($columnName));

            $columns[] = $column;
        }

        $this->entityMetadata = $this->getMockBuilder('\\perf\\Persistence\\EntityMetadata')->disableOriginalConstructor()->getMock();
        $this->entityMetadata->expects($this->atLeastOnce())->method('getClass')->will($this->returnValue($this->entityClass));
        $this->entityMetadata->expects($this->atLeastOnce())->method('getColumns')->will($this->returnValue($columns));
    }
}
