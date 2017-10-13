<?php

require_once __DIR__ . '/QueryBuilderTest.php';

use QueryBuilder\ColumnStatement;
use QueryBuilder\DeleteQuery;
use QueryBuilder\InvalidQueryException;
use QueryBuilder\RawValueStatement;

final class DeleteQueryTest extends QueryBuilderTest {
    public function testSimpleQuery() {
        $built = (new DeleteQuery('users'))
            ->addOrder('confirmed')
            ->addOrder('username', ORDER_DESC)
            ->setLimit(10)
            ->build();

        $this->printResults($built);

        $this->markTestIncomplete();
    }
}