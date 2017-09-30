<?php

require_once __DIR__ . '/../vendor/autoload.php';

use QueryBuilder\SelectQuery;
use QueryBuilder\ConditionCollection;

use PHPUnit\Framework\TestCase;

final class SelectQueryTest extends TestCase {
    public function testSimpleQuery() {
        $built = (new SelectQuery('users'))->build();

        $this->assertEquals($built->getQueryString(), 'SELECT * FROM `users`');
    }

    public function testQueryWithColumnNames() {
        $built = (new SelectQuery('users'))
            ->addColumn('username')
            ->addColumn('account_type')
            ->build();

        $this->assertEquals($built->getQueryString(), "SELECT `username`, `account_type` FROM `users`");
    }

    public function testQueryWithCondition() {
        $built = (new SelectQuery('users'))
            ->addColumn('username')
            ->setCondition('id', 532)
            ->build();

        $this->assertEquals($built->getQueryString(), "SELECT `username` FROM `users` WHERE `id` = ?");
        $this->assertEquals($built->getParameters(), [532]);
    }

    public function testQueryWithConditions() {
        $condition_collection = (new ConditionCollection(OPERATOR_AND))
            ->addCondition('id', 532)
            ->addCondition('confirmed', true);

        $built = (new SelectQuery('users'))
            ->addColumn('username')
            ->setConditionCollection($condition_collection)
            ->build();

        $this->assertEquals($built->getQueryString(), "SELECT `username` FROM `users` WHERE `id` = ? AND `confirmed` = ?");
        $this->assertEquals($built->getParameters(), [532, true]);
    }
}