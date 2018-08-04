<?php

require_once __DIR__ . '/QueryBuilderTest.php';

use QueryBuilder\ColumnStatement;
use QueryBuilder\InsertQuery;
use QueryBuilder\InvalidQueryException;
use QueryBuilder\RawValueStatement;

final class InsertQueryTest extends QueryBuilderTest {
    public function testSimpleQuery() {
        $built = (new InsertQuery('users'))
            ->addAssignment(new ColumnStatement('confirmed'), new RawValueStatement(true))
            ->addAssignment(new ColumnStatement('username'), new RawValueStatement('blah@blah.blah'))
            ->build();

        $this->assertEquals("INSERT INTO `users` SET `confirmed` = ?, `username` = ?", $built->getString());
        $this->assertEquals([true, 'blah@blah.blah'], $built->getParameters());

        $this->printResults($built);
    }

    public function testNoAssignmentsShouldThrowException() {
        $this->expectException(InvalidQueryException::class);
        $this->expectExceptionMessage('You must supply at least one assignment for an INSERT query');

        (new InsertQuery('users'))->build();
    }

    public function testLowPriority() {
        $built = (new InsertQuery('users'))
            ->addAssignment(new ColumnStatement('confirmed'), new RawValueStatement(true))
            ->setLowPriority(true)
            ->build();

        $this->assertEquals('INSERT LOW_PRIORITY INTO `users` SET `confirmed` = ?', $built->getString());

        $this->printResults($built);
    }

    public function testHighPriority() {
        $built = (new InsertQuery('users'))
            ->addAssignment(new ColumnStatement('confirmed'), new RawValueStatement(true))
            ->setHighPriority(true)
            ->build();

        $this->assertEquals('INSERT HIGH_PRIORITY INTO `users` SET `confirmed` = ?', $built->getString());

        $this->printResults($built);
    }

    public function testLowAndHighPriorityShouldThrowException() {
        $this->expectException(InvalidQueryException::class);
        $this->expectExceptionMessage('Query cannot be both high and low priority');

        (new InsertQuery('users'))
            ->setLowPriority(true)
            ->setHighPriority(true)
            ->build();
    }

    public function testHighAndLowPriorityShouldThrowException() {
        $this->expectException(InvalidQueryException::class);
        $this->expectExceptionMessage('Query cannot be both low and high priority');

        (new InsertQuery('users'))
            ->setHighPriority(true)
            ->setLowPriority(true)
            ->build();
    }
}