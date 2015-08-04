<?php

namespace perf\Persistence;

/**
 *
 */
class NaturalPrimaryKeyTest extends \PHPUnit_Framework_TestCase
{

    /**
     *
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage At least one column must be provided.
     */
    public function testWithoutColumnWillThrowException()
    {
        $columns = array();

        new NaturalPrimaryKey($columns);
    }
}
