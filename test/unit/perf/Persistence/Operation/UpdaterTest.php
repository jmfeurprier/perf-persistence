<?php

namespace perf\Persistence\Operation;

/**
 *
 */
class UpdaterTest extends \PHPUnit_Framework_TestCase
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

        $this->updater = new Updater();
        $this->updater->setEntityMetadataPool($this->entityMetadataPool);
        $this->updater->setConnectionPool($databaseConnectionPool);
        $this->updater->setEntityExporter($this->entityExporter);
    }

    /**
     *
     */
    public function testInsert()
    {
        $sqlTable = 'def';
        $row      = array(
            'foo' => 'bar',
            'baz' => 123,
        );
        $primaryKeyFilterClause     = '`foo` = ?';
        $primaryKeyFilterParameters = array(
            'bar',
        );
        $query      = "UPDATE {$sqlTable} SET `baz` = ? WHERE `foo` = ?";
        $parameters = array(
            123,
            'bar',
        );

        $this->entityExporter->expects($this->atLeastOnce())->method('export')->with($this->entityMetadata, $this->entity)->will($this->returnValue($row));

        $this->connection->expects($this->at(0))->method('escapeAndQuoteTable')->with($this->table)->will($this->returnValue($sqlTable));
        $this->connection->expects($this->at(1))->method('escapeAndQuoteField')->with('baz')->will($this->returnValue('`baz`'));
        $this->connection->expects($this->at(2))->method('execute')->with($query, $parameters);

        $primaryKeyColumn = $this->getMockBuilder('\\perf\\Persistence\\Column')->disableOriginalConstructor()->getMock();
        $primaryKeyColumn->expects($this->atLeastOnce())->method('getColumnName')->will($this->returnValue('foo'));

        $primaryKeyColumns = array(
            $primaryKeyColumn,
        );

        $primaryKeyFilter = $this->getMockBuilder('\\perf\\Db\\QueryFilter')->disableOriginalConstructor()->getMock();
        $primaryKeyFilter->expects($this->atLeastOnce())->method('getClause')->will($this->returnValue($primaryKeyFilterClause));
        $primaryKeyFilter->expects($this->atLeastOnce())->method('getParameters')->will($this->returnValue($primaryKeyFilterParameters));

        $this->primaryKey->expects($this->at(0))->method('getColumns')->will($this->returnValue($primaryKeyColumns));
        $this->primaryKey->expects($this->atLeastOnce())->method('getFilter')->with($this->connection, $this->entity)->will($this->returnValue($primaryKeyFilter));

        $this->updater->update($this->entity);
    }
}
