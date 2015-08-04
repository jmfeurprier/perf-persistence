<?php

namespace perf\Persistence\Operation;

/**
 *
 */
class OperatorFactoryTest extends \PHPUnit_Framework_TestCase
{

    /**
     *
     */
    protected function setUp()
    {
        $this->entityMetadataPool = $this->getMock('\\perf\\Persistence\\EntityMetadataPool');

        $this->databaseConnectionPool = $this->getMock('\\perf\\Db\\ConnectionPool');

        $this->operatorFactory = new OperatorFactory();
        $this->operatorFactory->setEntityMetadataPool($this->entityMetadataPool);
        $this->operatorFactory->setConnectionPool($this->databaseConnectionPool);
    }

    /**
     *
     */
    public function testGetInserterReturnsExpected()
    {
        $result = $this->operatorFactory->getInserter();

        $this->assertInstanceOf('\\perf\\Persistence\\Operation\\Inserter', $result);
    }

    /**
     *
     */
    public function testGetInserterWithMultiplieCallsWillReturnSameInstance()
    {
        $resultPrimary   = $this->operatorFactory->getInserter();
        $resultSecondary = $this->operatorFactory->getInserter();

        $this->assertSame($resultPrimary, $resultSecondary);
    }

    /**
     *
     */
    public function testGetUpdaterReturnsExpected()
    {
        $result = $this->operatorFactory->getUpdater();

        $this->assertInstanceOf('\\perf\\Persistence\\Operation\\Updater', $result);
    }

    /**
     *
     */
    public function testGetUpdaterWithMultiplieCallsWillReturnSameInstance()
    {
        $resultPrimary   = $this->operatorFactory->getUpdater();
        $resultSecondary = $this->operatorFactory->getUpdater();

        $this->assertSame($resultPrimary, $resultSecondary);
    }

    /**
     *
     */
    public function testGetDeleterReturnsExpected()
    {
        $result = $this->operatorFactory->getDeleter();

        $this->assertInstanceOf('\\perf\\Persistence\\Operation\\Deleter', $result);
    }

    /**
     *
     */
    public function testGetDeleterWithMultiplieCallsWillReturnSameInstance()
    {
        $resultPrimary   = $this->operatorFactory->getDeleter();
        $resultSecondary = $this->operatorFactory->getDeleter();

        $this->assertSame($resultPrimary, $resultSecondary);
    }

    /**
     *
     */
    public function testGetSelecterReturnsExpected()
    {
        $result = $this->operatorFactory->getSelecter();

        $this->assertInstanceOf('\\perf\\Persistence\\Operation\\Selecter', $result);
    }

    /**
     *
     */
    public function testGetSelecterWithMultiplieCallsWillReturnSameInstance()
    {
        $resultPrimary   = $this->operatorFactory->getSelecter();
        $resultSecondary = $this->operatorFactory->getSelecter();

        $this->assertSame($resultPrimary, $resultSecondary);
    }

    /**
     *
     */
    public function testGetCounterReturnsExpected()
    {
        $result = $this->operatorFactory->getCounter();

        $this->assertInstanceOf('\\perf\\Persistence\\Operation\\Counter', $result);
    }

    /**
     *
     */
    public function testGetCounterWithMultiplieCallsWillReturnSameInstance()
    {
        $resultPrimary   = $this->operatorFactory->getCounter();
        $resultSecondary = $this->operatorFactory->getCounter();

        $this->assertSame($resultPrimary, $resultSecondary);
    }
}
