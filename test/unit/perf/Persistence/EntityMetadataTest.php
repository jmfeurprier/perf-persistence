<?php

namespace perf\Persistence;

/**
 *
 */
class EntityMetadataTest extends \PHPUnit_Framework_TestCase
{

    /**
     *
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage Provided entity class is not a string.
     */
    public function testConstructorWithNonStringEntityClassWillThrowException()
    {
        $entityClass = array('\\stdClass');

        $storage = $this->getMockBuilder('\\perf\\Persistence\\Storage')->disableOriginalConstructor()->getMock();

        $column = $this->getMockBuilder('\\perf\\Persistence\\Column')->disableOriginalConstructor()->getMock();

        $columns = array(
            $column,
        );

        $primaryKey = $this->getMockBuilder('\\perf\\Persistence\\PrimaryKey')->disableOriginalConstructor()->getMock();

        $entityMetadata = new EntityMetadata($entityClass, $storage, $columns, $primaryKey);
    }

    /**
     *
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage No column provided.
     */
    public function testConstructorWithoutColumnsWillThrowException()
    {
        $entityClass = '\\Foo';

        $storage = $this->getMockBuilder('\\perf\\Persistence\\Storage')->disableOriginalConstructor()->getMock();

        $columns = array();

        $primaryKey = $this->getMockBuilder('\\perf\\Persistence\\PrimaryKey')->disableOriginalConstructor()->getMock();

        $entityMetadata = new EntityMetadata($entityClass, $storage, $columns, $primaryKey);
    }
}
