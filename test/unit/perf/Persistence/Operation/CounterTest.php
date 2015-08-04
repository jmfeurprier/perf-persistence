<?php

namespace perf\Persistence\Operation;

/**
 *
 */
class CounterTest extends \PHPUnit_Framework_TestCase
{

    private $connectionId = 'CONNECTION_ID';

    /**
     *
     */
    protected function setUp()
    {
        $this->entityStorage = $this->getMockBuilder('\\perf\\Persistence\\Storage')->disableOriginalConstructor()->getMock();

        $this->entityMetadata = $this->getMockBuilder('\\perf\\Persistence\\EntityMetadata')->disableOriginalConstructor()->getMock();

        $this->entityMetadataPool = $this->getMock('\\perf\\Persistence\\EntityMetadataPool');

        $this->connection = $this->getMockBuilder('\\perf\\Db\\Connection')->disableOriginalConstructor()->getMock();

        $this->databaseConnectionPool = $this->getMock('\\perf\\Db\\ConnectionPool');
        $this->databaseConnectionPool->expects($this->atLeastOnce())->method('fetch')->with($this->connectionId)->will($this->returnValue($this->connection));

        $this->counter = new Counter();
        $this->counter->setEntityMetadataPool($this->entityMetadataPool);
        $this->counter->setConnectionPool($this->databaseConnectionPool);
    }

    /**
     *
     */
    public function testCountWithoutFilter()
    {
        $entityClass = '\\Entity\\Class';
        $table       = 'TABLE';
        $sqlTable    = 'TABLE_SQL';
        $query       = "SELECT COUNT(0) FROM {$sqlTable}";
        $queryResult = '123';

        $this->entityStorage->expects($this->atLeastOnce())->method('getConnectionId')->will($this->returnValue($this->connectionId));
        $this->entityStorage->expects($this->atLeastOnce())->method('getTable')->will($this->returnValue($table));

        $this->entityMetadata->expects($this->atLeastOnce())->method('getStorage')->will($this->returnValue($this->entityStorage));

        $this->entityMetadataPool->expects($this->atLeastOnce())->method('fetch')->with($entityClass)->will($this->returnValue($this->entityMetadata));

        $this->connection->expects($this->at(0))->method('escapeAndQuoteTable')->with($table)->will($this->returnValue($sqlTable));
        $this->connection->expects($this->at(1))->method('queryValue')->with($query)->will($this->returnValue($queryResult));

        $result = $this->counter->count($entityClass);

        $this->assertInternalType('integer', $result);
        $this->assertEquals($queryResult, $result);
    }

    /**
     *
     */
    public function testCountWithFilter()
    {
        $entityClass           = '\\Entity\\Class';
        $table                 = 'table';
        $sqlTable              = '`table_sql`';
        $queryFilterClause     = '`column` = ?';
        $queryFilterParameters = array(
            'parameter',
        );
        $query       = "SELECT COUNT(0) FROM {$sqlTable} WHERE {$queryFilterClause}";
        $queryResult = '123';

        $this->entityStorage->expects($this->atLeastOnce())->method('getConnectionId')->will($this->returnValue($this->connectionId));
        $this->entityStorage->expects($this->atLeastOnce())->method('getTable')->will($this->returnValue($table));

        $this->entityMetadata->expects($this->atLeastOnce())->method('getStorage')->will($this->returnValue($this->entityStorage));

        $this->entityMetadataPool->expects($this->atLeastOnce())->method('fetch')->with($entityClass)->will($this->returnValue($this->entityMetadata));

        $this->connection->expects($this->at(0))->method('escapeAndQuoteTable')->with($table)->will($this->returnValue($sqlTable));
        $this->connection->expects($this->at(1))->method('queryValue')->with($query, $queryFilterParameters)->will($this->returnValue($queryResult));

        $queryFilter = $this->getMockBuilder('\\perf\\Db\\QueryFilter')->disableOriginalConstructor()->getMock();
        $queryFilter->expects($this->atLeastOnce())->method('getClause')->will($this->returnValue($queryFilterClause));
        $queryFilter->expects($this->atLeastOnce())->method('getParameters')->will($this->returnValue($queryFilterParameters));

        $result = $this->counter->count($entityClass, $queryFilter);

        $this->assertInternalType('integer', $result);
        $this->assertEquals($queryResult, $result);
    }
}
