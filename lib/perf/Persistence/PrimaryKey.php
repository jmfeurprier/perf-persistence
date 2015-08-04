<?php

namespace perf\Persistence;

/**
 *
 *
 */
interface PrimaryKey
{

    /**
     *
     *
     * @return Column[]
     */
    public function getColumns();

    /**
     *
     *
     * @param \perf\Db\Connection $connection
     * @param object $entity
     * @return \perf\Db\QueryFilter
     */
    public function getFilter(\perf\Db\Connection $connection, $entity);

    /**
     *
     *
     * @param \perf\Db\Connection $connection
     * @param object $entity
     * @return void
     */
    public function bind(\perf\Db\Connection $connection, $entity);

    /**
     *
     *
     * @param object $entity
     * @return void
     */
    public function unbind($entity);
}
