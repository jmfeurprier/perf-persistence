<?php

namespace perf\Persistence;

/**
 *
 */
class IncrementalPrimaryKeyTest extends \PHPUnit_Framework_TestCase
{

    /**
     *
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage Exactly one column must be provided.
     */
    public function testWithoutColumnWillThrowException()
    {
        $columns = array();

        new IncrementalPrimaryKey($columns);
    }
}
