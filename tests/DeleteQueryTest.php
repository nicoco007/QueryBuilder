<?php

require_once __DIR__ . '/QueryBuilderTest.php';

use QueryBuilder\ColumnStatement;
use QueryBuilder\ConditionCollection;
use QueryBuilder\DeleteQuery;
use QueryBuilder\RawValueStatement;

final class DeleteQueryTest extends QueryBuilderTest {
    public function testSimpleQuery() {
        $built = (new DeleteQuery('users'))
            ->build();

        $this->assertEquals("DELETE FROM `users`", $built->getString());
        $this->assertEquals([], $built->getParameters());

        $this->printResults($built);
    }

    public function testAllSettings() {
        $conditions = (new ConditionCollection(OPERATOR_AND))
            ->addCondition(new ColumnStatement('confirmed'), new RawValueStatement(false));

        $built = (new DeleteQuery('users'))
            ->addOrder('confirmed')
            ->addOrder('username', ORDER_DESC)
            ->setLimit(10)
            ->setLowPriority(true)
            ->setIgnore(true)
            ->setQuick(true)
            ->setConditionCollection($conditions)
            ->build();

        $this->assertEquals("DELETE LOW_PRIORITY QUICK IGNORE FROM `users` WHERE `confirmed` = ? ORDER BY `confirmed` ASC, `username` DESC LIMIT 10", $built->getString());
        $this->assertEquals([false], $built->getParameters());

        $this->printResults($built);
    }
}