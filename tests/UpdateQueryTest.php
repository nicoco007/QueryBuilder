<?php

require_once __DIR__ . '/QueryBuilderTest.php';

use QueryBuilder\ColumnStatement;
use QueryBuilder\RawValueStatement;
use QueryBuilder\UpdateQuery;

final class UpdateQueryTest extends QueryBuilderTest {
    public function testSimpleQuery() {
        $built = (new UpdateQuery('users'))
            ->addAssignment(new ColumnStatement('email'), new RawValueStatement('blah'))
            ->setCondition(new ColumnStatement('email'), new RawValueStatement('ooga booga'))
            ->build();

        $this->assertEquals("UPDATE `users` SET `email` = ? WHERE `email` = ?", $built->getString());
        $this->assertEquals(['blah', 'ooga booga'], $built->getParameters());

        $this->printResults($built);
    }

    public function testEverything() {
        $built = (new UpdateQuery('users'))
            ->setIgnore(true)
            ->setLowPriority(true)
            ->setLimit(10)
            ->addAssignment(new ColumnStatement('confirmed'), new RawValueStatement(true))
            ->addAssignment(new ColumnStatement('username'), new RawValueStatement('blah@blah.blah'))
            ->setCondition(new ColumnStatement('confirmed'), new RawValueStatement(false))
            ->build();

        $this->assertEquals("UPDATE LOW_PRIORITY IGNORE `users` SET `confirmed` = ?, `username` = ? WHERE `confirmed` = ? LIMIT 10", $built->getString());
        $this->assertEquals([true, 'blah@blah.blah', false], $built->getParameters());

        $this->printResults($built);
    }

    public function testUnsafeQueryException() {
        $this->expectException(\QueryBuilder\UnsafeUpdateException::class);

        (new UpdateQuery('users'))
            ->addAssignment(new ColumnStatement('email'), new RawValueStatement('blah'))
            ->build();
    }

    public function testUnsafeQuery() {
        $built = (new UpdateQuery('users'))
            ->addAssignment(new ColumnStatement('email'), new RawValueStatement('blah'))
            ->setAllowUnsafeUpdate(true)
            ->build();

        $this->assertEquals("UPDATE `users` SET `email` = ?", $built->getString());
        $this->assertEquals(['blah'], $built->getParameters());

        $this->printResults($built);
    }
}