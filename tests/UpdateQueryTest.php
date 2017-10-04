<?php
/**
 * Created by PhpStorm.
 * User: Nicolas
 * Date: 2017-10-04
 * Time: 13:38
 */

use PHPUnit\Framework\TestCase;

use QueryBuilder\ColumnStatement;
use QueryBuilder\RawStatement;
use QueryBuilder\UpdateQuery;

class UpdateQueryTest extends TestCase {
    public function testSimpleQuery() {
        $built = (new UpdateQuery('users'))
            ->addAssignment(new ColumnStatement('email'), new RawStatement('blé'))
            ->build();

        $this->assertTrue(true);

        $this->printResults($built);
    }

    /**
     * @param QueryBuilder\BuiltQuery $built
     */
    public function printResults($built) {
        printf("Query: %s\nParameters: %s\n\n", $built->getString(), json_encode($built->getParameters(), JSON_UNESCAPED_UNICODE));
    }
}