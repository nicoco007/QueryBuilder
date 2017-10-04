<?php

abstract class QueryBuilderTest extends \PHPUnit\Framework\TestCase {
    /**
     * @param QueryBuilder\BuiltQuery $built
     */
    public function printResults($built) {
        printf("Query: %s\nParameters: %s\n\n", $built->getString(), json_encode($built->getParameters(), JSON_UNESCAPED_UNICODE));
    }
}