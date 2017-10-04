<?php

use QueryBuilder\ColumnStatement;
use QueryBuilder\RawStatement;
use QueryBuilder\UpdateQuery;

class UpdateQueryTest extends QueryBuilderTest {
    public function testSimpleQuery() {
        $built = (new UpdateQuery('users'))
            ->addAssignment(new ColumnStatement('email'), new RawStatement('blah'))
            ->build();

        $this->markTestIncomplete();

        $this->printResults($built);
    }
}