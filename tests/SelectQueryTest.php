<?php

require_once __DIR__ . '/QueryBuilderTest.php';

use QueryBuilder\SelectQuery;
use QueryBuilder\ConditionCollection;
use QueryBuilder\Condition;
use QueryBuilder\Join;
use QueryBuilder\MaxStatement;
use QueryBuilder\CaseStatement;
use QueryBuilder\ColumnStatement;
use QueryBuilder\RawValueStatement;

final class SelectQueryTest extends QueryBuilderTest {

    public function testSimpleQuery() {
        $built = (new SelectQuery('users'))->build();

        $this->assertEquals('SELECT * FROM `users`', $built->getString());

        $this->printResults($built);
    }

    public function testQueryWithColumnNames() {
        $built = (new SelectQuery('users'))
            ->addStatement(new ColumnStatement('username'))
            ->addStatement(new ColumnStatement('account_type'))
            ->build();

        $this->assertEquals("SELECT `username`, `account_type` FROM `users`", $built->getString());

        $this->printResults($built);
    }

    public function testQueryWithCondition() {
        $built = (new SelectQuery('users'))
            ->addStatement(new ColumnStatement('username'))
            ->setCondition(new ColumnStatement('account_type'), new RawValueStatement('user'))
            ->build();

        $this->assertEquals("SELECT `username` FROM `users` WHERE `account_type` = ?", $built->getString());
        $this->assertEquals(['user'], $built->getParameters());

        $this->printResults($built);
    }

    public function testQueryWithConditions() {
        $conditions = (new ConditionCollection(OPERATOR_OR))
            ->addCondition(new ColumnStatement('account_type'), new RawValueStatement('user'))
            ->addCondition(new ColumnStatement('account_type'), new RawValueStatement('admin'));

        $condition_collection = (new ConditionCollection(OPERATOR_AND))
            ->addCondition(new ColumnStatement('account_type'), new RawValueStatement('user'))
            ->addCondition(new ColumnStatement('confirmed'), new RawValueStatement(true))
            ->addChild($conditions);

        $built = (new SelectQuery('users'))
            ->addStatement(new ColumnStatement('username'))
            ->setConditionCollection($condition_collection)
            ->build();

        $this->assertEquals("SELECT `username` FROM `users` WHERE `account_type` = ? AND `confirmed` = ? AND (`account_type` = ? OR `account_type` = ?)", $built->getString());
        $this->assertEquals(['user', true, 'user', 'admin'], $built->getParameters());

        $this->printResults($built);
    }

    public function testQueryWithComplexJoin() {
        $query = (new SelectQuery('forms_submissions_data'))
            ->addStatement(new ColumnStatement('submission_id'))
            ->addStatement(new MaxStatement((new CaseStatement())->addCase(new Condition(new ColumnStatement('key'), new RawValueStatement(1286)), new ColumnStatement('value'))), 'camper_first_name')
            ->addStatement(new MaxStatement((new CaseStatement())->addCase(new Condition(new ColumnStatement('key'), new RawValueStatement(2)), new ColumnStatement('value'))), 'camper_last_name')
            ->addStatement(new MaxStatement((new CaseStatement())->addCase(new Condition(new ColumnStatement('key'), new RawValueStatement(3)), new ColumnStatement('value'))), 'camper_dob')
            ->setGroupBy(new ColumnStatement('submission_id'));

        $join = (new Join($query, 'c'))
            ->setCondition(new ColumnStatement('submission_id'), new ColumnStatement('camper_id', 'r'));

        $join2 = (new Join(new ColumnStatement('programs'), 'p'))
            ->setCondition(new ColumnStatement('id', 'p'), new ColumnStatement('program_id', 'r'));

        $join3 = (new Join(new ColumnStatement('locations'), 'l'))
            ->setCondition(new ColumnStatement('id', 'l'), new ColumnStatement('location_id', 'r'));

        $built = (new SelectQuery('registrations'))
            ->setAlias('r')
            ->addJoin($join)
            ->addJoin($join2)
            ->addJoin($join3)
            ->build();

        $this->assertEquals("SELECT * FROM `registrations` AS `r` JOIN (SELECT `submission_id`, MAX(CASE WHEN `key` = ? THEN `value` END) AS camper_first_name, MAX(CASE WHEN `key` = ? THEN `value` END) AS camper_last_name, MAX(CASE WHEN `key` = ? THEN `value` END) AS camper_dob FROM `forms_submissions_data` GROUP BY `submission_id`) AS `c` ON `submission_id` = `r`.`camper_id` JOIN `programs` AS `p` ON `p`.`id` = `r`.`program_id` JOIN `locations` AS `l` ON `l`.`id` = `r`.`location_id`", $built->getString());
        $this->assertEquals([1286, 2, 3], $built->getParameters());

        $this->printResults($built);
    }
}