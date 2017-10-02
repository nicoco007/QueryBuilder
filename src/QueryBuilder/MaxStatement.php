<?php
/**
 * Created by PhpStorm.
 * User: Nicolas
 * Date: 2017-09-30
 * Time: 19:59
 */

namespace QueryBuilder;


class MaxStatement extends Statement {
    /** @var Statement */
    private $statement;

    /**
     * @param Statement $statement
     */
    public function __construct($statement) {
        $this->statement = $statement;
    }

    public function build() {
        $built = $this->statement->build();
        return new BuiltQuery(sprintf('MAX(%s)', $built->getQueryString()), $built->getParameters());
    }
}