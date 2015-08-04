<?php

namespace perf\Persistence\Operation;

/**
 *
 */
class InserterTest extends \PHPUnit_Framework_TestCase
{

    private $connectionId = 'CONNECTION_ID';
    private $table        = 'TABLE';

    /**
     *
     */
    protected function setUp()
    {
        $this->entity = new \stdClass();

        $this->entityClass = get_class($this->entity);

        $this->connection = $this->getMockBuilder('\\perf\\Db\\Connection')->disableOriginalConstructor()->getMock();

        $databaseConnectionPool = $this->getMock('\\perf\\Db\\ConnectionPool');
        $databaseConnectionPool->expects($this->any())->method('fetch')->with($this->connectionId)->will($this->returnValue($this->connection));

        $entityStorage = $this->getMockBuilder('\\perf\\Persistence\\Storage')->disableOriginalConstructor()->getMock();
        $entityStorage->expects($this->atLeastOnce())->method('getConnectionId')->will($this->returnValue($this->connectionId));
        $entityStorage->expects($this->atLeastOnce())->method('getTable')->will($this->returnValue($this->table));

        $this->primaryKey = $this->getMockBuilder('\\perf\\Persistence\\PrimaryKey')->disableOriginalConstructor()->getMock();

        $this->entityMetadata = $this->getMockBuilder('\\perf\\Persistence\\EntityMetadata')->disableOriginalConstructor()->getMock();
        $this->entityMetadata->expects($this->atLeastOnce())->method('getStorage')->will($this->returnValue($entityStorage));
        $this->entityMetadata->expects($this->atLeastOnce())->method('getPrimaryKey')->will($this->returnValue($this->primaryKey));

        $this->entityMetadataPool = $this->getMock('\\perf\\Persistence\\EntityMetadataPool');
        $this->entityMetadataPool->expects($this->atLeastOnce())->method('fetch')->with($this->entityClass)->will($this->returnValue($this->entityMetadata));

        $this->entityExporter = $this->getMock('\\perf\\Persistence\\Operation\\EntityExporter');

        $this->inserter = new Inserter();
        $this->inserter->setEntityMetadataPool($this->entityMetadataPool);
        $this->inserter->setConnectionPool($databaseConnectionPool);
        $this->inserter->setEntityExporter($this->entityExporter);
    }

    /**
     *
     */
    public function testInsert()
    {
        $sqlTable   = 'def';
        $query      = "INSERT INTO {$sqlTable} (`foo`, `baz`) VALUES (?, ?)";
        $parameters = array(
            'bar',
            123,
        );
        $row = array(
            'foo' => 'bar',
            'baz' => 123,
        );

        $this->entityExporter->expects($this->atLeastOnce())->method('export')->with($this->entityMetadata, $this->entity)->will($this->returnValue($row));

        $this->connection->expects($this->at(0))->method('escapeAndQuoteTable')->with($this->table)->will($this->returnValue($sqlTable));
        $this->connection->expects($this->at(1))->method('escapeAndQuoteField')->with('foo')->will($this->returnValue('`foo`'));
        $this->connection->expects($this->at(2))->method('escapeAndQuoteField')->with('baz')->will($this->returnValue('`baz`'));
        $this->connection->expects($this->at(3))->method('execute')->with($query, $parameters);

        $this->primaryKey->expects($this->once())->method('bind')->with($this->connection, $this->entity);

        $this->inserter->insert($this->entity);
    }
}
