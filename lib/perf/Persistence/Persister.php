<?php

namespace perf\Persistence;

use \perf\Db\ConnectionPool;
use \perf\Db\QueryFilter;
use \perf\Db\QuerySorting;

/**
 *
 *
 */
class Persister
{

    /**
     *
     *
     * @var \perf\Persistence\Operation\OperatorFactory
     */
    private $operatorFactory;

    /**
     *
     *
     * @return PersisterBuilder
     */
    public static function createBuilder()
    {
        return new PersisterBuilder();
    }

    /**
     *
     *
     * @param \perf\Persistence\Operation\OperatorFactory $factory
     * @return void
     */
    public function setOperatorFactory(\perf\Persistence\Operation\OperatorFactory $factory)
    {
        $this->operatorFactory = $factory;
    }

    /**
     * Inserts provided entity into storage.
     *
     * @param object $entity
     * @return void
     * @throws \DomainException
     * @throws \RuntimeException
     */
    public function insert($entity)
    {
        $this->operatorFactory->getInserter()->insert($entity);
    }

    /**
     * Updates provided entity within storage.
     *
     * @param object $entity
     * @return void
     * @throws \DomainException
     * @throws \RuntimeException
     */
    public function update($entity)
    {
        $this->operatorFactory->getUpdater()->update($entity);
    }

    /**
     * Removes provided entity from storage.
     *
     * @param object $entity
     * @return void
     * @throws \DomainException
     * @throws \RuntimeException
     */
    public function delete($entity)
    {
        $this->operatorFactory->getDeleter()->delete($entity);
    }

    /**
     *
     *
     * @param string $entityClass
     * @param QueryFilter $filter
     * @param null|QuerySorting $sorting
     * @param null|int $limit
     * @return void
     * @throws \InvalidArgumentException
     * @throws \RuntimeException
     */
    public function deleteMany($entityClass, QueryFilter $filter, QuerySorting $sorting = null, $limit = null)
    {
        $this->operatorFactory->getDeleter()->deleteMany($entityClass, $filter, $sorting, $limit);
    }

    /**
     *
     *
     * @param string $entityClass
     * @param QueryFilter $filter
     * @return Entity
     * @throws \DomainException
     * @throws \RuntimeException
     */
    public function findOne($entityClass, QueryFilter $filter)
    {
        $entity = $this->tryFindOne($entityClass, $filter);

        if ($entity) {
            return $entity;
        }

        throw new \DomainException('Provided filter did not match any entity.');
    }

    /**
     *
     *
     * @param string $entityClass
     * @param QueryFilter $filter
     * @return null|Entity
     * @throws \RuntimeException
     */
    public function tryFindOne($entityClass, QueryFilter $filter)
    {
        static $sorting = null;
        static $offset  = 0;
        static $limit   = 2;

        $entities = $this->findMany($entityClass, $filter, $sorting, $offset, $limit);

        if (count($entities) > 1) {
            throw new \RuntimeException('Provided filter matched more than one entity.');
        }

        if (1 === count($entities)) {
            $entity = reset($entities);

            return $entity;
        }

        return null;
    }

    /**
     *
     *
     * @param string $entityClass
     * @param null|QueryFilter $filter
     * @param null|QuerySorting $sorting
     * @param int $offset
     * @param null|int $limit
     * @return array
     */
    public function findMany(
        $entityClass,
        QueryFilter $filter = null,
        QuerySorting $sorting = null,
        $offset = 0,
        $limit = null
    ) {
        $entities = array();

        foreach ($this->iterate($entityClass, $filter, $sorting, $offset, $limit) as $entity) {
            $entities[] = $entity;
        }

        return $entities;
    }

    /**
     *
     *
     * @param string $entityClass
     * @param null|QueryFilter $filter
     * @param null|QuerySorting $sorting
     * @param int $offset
     * @param null|int $limit
     * @return EntityIterator
     * @throws \InvalidArgumentException
     */
    public function iterate(
        $entityClass,
        QueryFilter $filter = null,
        QuerySorting $sorting = null,
        $offset = 0,
        $limit = null
    ) {
        return $this->operatorFactory->getSelecter()->select($entityClass, $filter, $sorting, $offset, $limit);
    }

    /**
     *
     *
     * @param string $entityClass
     * @param QueryFilter $filter
     * @return bool
     */
    public function exist($entityClass, QueryFilter $filter)
    {
        $count = $this->count($entityClass, $filter);

        return ($count > 0);
    }

    /**
     *
     *
     * @param string $entityClass
     * @param null|QueryFilter $filter
     * @return int
     */
    public function count($entityClass, QueryFilter $filter = null)
    {
        return $this->operatorFactory->getCounter()->count($entityClass, $filter);
    }
}
