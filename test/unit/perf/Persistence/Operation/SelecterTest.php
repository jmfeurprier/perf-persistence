<?php

namespace perf\Persistence\Operation;

/**
 *
 */
class SelecterTest extends \PHPUnit_Framework_TestCase
{

    private $connectionId = 'CONNECTION_ID';
    private $entityClass  = '\\Entity\\Class';
    private $table        = 'TABLE';

    /**
     *
     */
    protected function setUp()
    {
        $this->connection = $this->getMockBuilder('\\perf\\Db\\Connection')->disableOriginalConstructor()->getMock();

        $databaseConnectionPool = $this->getMock('\\perf\\Db\\ConnectionPool');
        $databaseConnectionPool->expects($this->any())->method('fetch')->with($this->connectionId)->will($this->returnValue($this->connection));

        $this->entityMetadata = $this->getMockBuilder('\\perf\\Persistence\\EntityMetadata')->disableOriginalConstructor()->getMock();

        $this->entityMetadataPool = $this->getMock('\\perf\\Persistence\\EntityMetadataPool');
        $this->entityMetadataPool->expects($this->atLeastOnce())->method('fetch')->with($this->entityClass)->will($this->returnValue($this->entityMetadata));

        $entityImporter = $this->getMock('\\perf\\Persistence\\Operation\\EntityImporter');

        $this->selecter = new Selecter();
        $this->selecter->setConnectionPool($databaseConnectionPool);
        $this->selecter->setEntityMetadataPool($this->entityMetadataPool);
        $this->selecter->setEntityImporter($entityImporter);
    }

    /**
     *
     */
    public function testSelectAll()
    {
        $columnName    = 'baz';
        $sqlColumnName = 'qux';
        $sqlTable      = 'def';
        $query         = "SELECT {$sqlColumnName} FROM {$sqlTable}";

        $this->buildEntityMetadata(array($columnName));

        $queryResult = $this->getMockBuilder('\\perf\\Db\\QueryResult')->disableOriginalConstructor()->getMock();

        $this->connection->expects($this->at(0))->method('escapeAndQuoteField')->with($columnName)->will($this->returnValue($sqlColumnName));
        $this->connection->expects($this->at(1))->method('escapeAndQuoteTable')->with($this->table)->will($this->returnValue($sqlTable));
        $this->connection->expects($this->at(2))->method('query')->with($query)->will($this->returnValue($queryResult));

        $result = $this->selecter->select($this->entityClass);

        $this->assertInstanceOf('\\perf\\Persistence\\EntityIterator', $result);
    }

    /**
     *
     */
    public function testSelectWithTwoColumns()
    {
        $columnNamePrimary      = 'baz';
        $sqlColumnNamePrimary   = 'qux';
        $columnNameSecondary    = 'abc';
        $sqlColumnNameSecondary = 'def';
        $sqlTable               = 'jkl';
        $query                  = "SELECT {$sqlColumnNamePrimary}, {$sqlColumnNameSecondary} FROM {$sqlTable}";

        $this->buildEntityMetadata(array($columnNamePrimary, $columnNameSecondary));

        $queryResult = $this->getMockBuilder('\\perf\\Db\\QueryResult')->disableOriginalConstructor()->getMock();

        $this->connection->expects($this->at(0))->method('escapeAndQuoteField')->with($columnNamePrimary)->will($this->returnValue($sqlColumnNamePrimary));
        $this->connection->expects($this->at(1))->method('escapeAndQuoteField')->with($columnNameSecondary)->will($this->returnValue($sqlColumnNameSecondary));
        $this->connection->expects($this->at(2))->method('escapeAndQuoteTable')->with($this->table)->will($this->returnValue($sqlTable));
        $this->connection->expects($this->at(3))->method('query')->with($query)->will($this->returnValue($queryResult));

        $result = $this->selecter->select($this->entityClass);

        $this->assertInstanceOf('\\perf\\Persistence\\EntityIterator', $result);
    }

    /**
     *
     */
    public function testSelectWithLimit()
    {
        $columnName    = 'baz';
        $sqlColumnName = 'qux';
        $sqlTable      = 'def';
        $limit         = 123;
        $query         = "SELECT {$sqlColumnName} FROM {$sqlTable} LIMIT {$limit}";

        $this->buildEntityMetadata(array($columnName));

        $queryResult = $this->getMockBuilder('\\perf\\Db\\QueryResult')->disableOriginalConstructor()->getMock();

        $this->connection->expects($this->at(0))->method('escapeAndQuoteField')->with($columnName)->will($this->returnValue($sqlColumnName));
        $this->connection->expects($this->at(1))->method('escapeAndQuoteTable')->with($this->table)->will($this->returnValue($sqlTable));
        $this->connection->expects($this->at(2))->method('query')->with($query)->will($this->returnValue($queryResult));

        $result = $this->selecter->select($this->entityClass, null, null, 0, $limit);

        $this->assertInstanceOf('\\perf\\Persistence\\EntityIterator', $result);
    }

    /**
     *
     */
    public function testSelectWithOffset()
    {
        $columnName    = 'baz';
        $sqlColumnName = 'qux';
        $sqlTable      = 'def';
        $offset        = 123;
        $limit         = PHP_INT_MAX;
        $query         = "SELECT {$sqlColumnName} FROM {$sqlTable} LIMIT {$limit} OFFSET {$offset}";

        $this->buildEntityMetadata(array($columnName));

        $queryResult = $this->getMockBuilder('\\perf\\Db\\QueryResult')->disableOriginalConstructor()->getMock();

        $this->connection->expects($this->at(0))->method('escapeAndQuoteField')->with($columnName)->will($this->returnValue($sqlColumnName));
        $this->connection->expects($this->at(1))->method('escapeAndQuoteTable')->with($this->table)->will($this->returnValue($sqlTable));
        $this->connection->expects($this->at(2))->method('query')->with($query)->will($this->returnValue($queryResult));

        $result = $this->selecter->select($this->entityClass, null, null, $offset);

        $this->assertInstanceOf('\\perf\\Persistence\\EntityIterator', $result);
    }

    /**
     *
     */
    public function testSelectWithOffsetAndLimit()
    {
        $columnName    = 'baz';
        $sqlColumnName = 'qux';
        $sqlTable      = 'def';
        $offset        = 123;
        $limit         = 234;
        $query         = "SELECT {$sqlColumnName} FROM {$sqlTable} LIMIT {$limit} OFFSET {$offset}";

        $this->buildEntityMetadata(array($columnName));

        $queryResult = $this->getMockBuilder('\\perf\\Db\\QueryResult')->disableOriginalConstructor()->getMock();

        $this->connection->expects($this->at(0))->method('escapeAndQuoteField')->with($columnName)->will($this->returnValue($sqlColumnName));
        $this->connection->expects($this->at(1))->method('escapeAndQuoteTable')->with($this->table)->will($this->returnValue($sqlTable));
        $this->connection->expects($this->at(2))->method('query')->with($query)->will($this->returnValue($queryResult));

        $result = $this->selecter->select($this->entityClass, null, null, $offset, $limit);

        $this->assertInstanceOf('\\perf\\Persistence\\EntityIterator', $result);
    }

    /**
     *
     */
    public function testSelectWithNegativeOffsetWillThrowException()
    {
        $columnName    = 'baz';
        $sqlColumnName = 'qux';
        $sqlTable      = 'def';
        $offset        = -123;
        $limit         = 234;

        $this->buildEntityMetadata(array($columnName));

        $queryResult = $this->getMockBuilder('\\perf\\Db\\QueryResult')->disableOriginalConstructor()->getMock();

        $this->connection->expects($this->at(0))->method('escapeAndQuoteField')->with($columnName)->will($this->returnValue($sqlColumnName));
        $this->connection->expects($this->at(1))->method('escapeAndQuoteTable')->with($this->table)->will($this->returnValue($sqlTable));

        $this->setExpectedException('\\InvalidArgumentException', 'Offset must be equal to or greater than zero.');

        $this->selecter->select($this->entityClass, null, null, $offset, $limit);
    }

    /**
     *
     */
    public function testSelectWithNonIntegerOffsetWillThrowException()
    {
        $columnName    = 'baz';
        $sqlColumnName = 'qux';
        $sqlTable      = 'def';
        $offset        = null;
        $limit         = 234;

        $this->buildEntityMetadata(array($columnName));

        $queryResult = $this->getMockBuilder('\\perf\\Db\\QueryResult')->disableOriginalConstructor()->getMock();

        $this->connection->expects($this->at(0))->method('escapeAndQuoteField')->with($columnName)->will($this->returnValue($sqlColumnName));
        $this->connection->expects($this->at(1))->method('escapeAndQuoteTable')->with($this->table)->will($this->returnValue($sqlTable));

        $this->setExpectedException('\\InvalidArgumentException', 'Offset must be an integer.');

        $this->selecter->select($this->entityClass, null, null, $offset, $limit);
    }

    /**
     *
     */
    public function testSelectWithNegativeLimitWillThrowException()
    {
        $columnName    = 'baz';
        $sqlColumnName = 'qux';
        $sqlTable      = 'def';
        $offset        = 123;
        $limit         = -234;

        $this->buildEntityMetadata(array($columnName));

        $queryResult = $this->getMockBuilder('\\perf\\Db\\QueryResult')->disableOriginalConstructor()->getMock();

        $this->connection->expects($this->at(0))->method('escapeAndQuoteField')->with($columnName)->will($this->returnValue($sqlColumnName));
        $this->connection->expects($this->at(1))->method('escapeAndQuoteTable')->with($this->table)->will($this->returnValue($sqlTable));

        $this->setExpectedException('\\InvalidArgumentException', 'Limit must be greater than zero.');

        $this->selecter->select($this->entityClass, null, null, $offset, $limit);
    }

    /**
     *
     */
    public function testSelectWithUnexpectedLimitTypeWillThrowException()
    {
        $columnName    = 'baz';
        $sqlColumnName = 'qux';
        $sqlTable      = 'def';
        $offset        = 123;
        $limit         = '234';

        $this->buildEntityMetadata(array($columnName));

        $queryResult = $this->getMockBuilder('\\perf\\Db\\QueryResult')->disableOriginalConstructor()->getMock();

        $this->connection->expects($this->at(0))->method('escapeAndQuoteField')->with($columnName)->will($this->returnValue($sqlColumnName));
        $this->connection->expects($this->at(1))->method('escapeAndQuoteTable')->with($this->table)->will($this->returnValue($sqlTable));

        $this->setExpectedException('\\InvalidArgumentException', 'Limit must be an integer or null.');

        $this->selecter->select($this->entityClass, null, null, $offset, $limit);
    }

    /**
     *
     */
    private function buildEntityMetadata(array $columnNames)
    {
        $entityStorage = $this->getMockBuilder('\\perf\\Persistence\\Storage')->disableOriginalConstructor()->getMock();
        $entityStorage->expects($this->atLeastOnce())->method('getConnectionId')->will($this->returnValue($this->connectionId));
        $entityStorage->expects($this->atLeastOnce())->method('getTable')->will($this->returnValue($this->table));

        $columns = array();

        foreach ($columnNames as $columnName) {
            $column = $this->getMockBuilder('\\perf\\Persistence\\Column')->disableOriginalConstructor()->getMock();
            $column->expects($this->atLeastOnce())->method('getColumnName')->will($this->returnValue($columnName));

            $columns[] = $column;
        }

        $this->entityMetadata->expects($this->atLeastOnce())->method('getStorage')->will($this->returnValue($entityStorage));
        $this->entityMetadata->expects($this->atLeastOnce())->method('getColumns')->will($this->returnValue($columns));
    }
}
