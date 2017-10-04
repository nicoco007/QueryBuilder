<?php

namespace QueryBuilder;


class MaxStatement extends Statement {
    /** @var Statement */
    private $statement;

    /**
     * @param Statement $statement
     */
    public function __construct($statement) {
        if (!($statement instanceof Statement))
            throw new \InvalidArgumentException('Expected $statement to be Statement, got '. Util::get_type($statement));

        $this->statement = $statement;
    }

    /**
     * @return BuiltStatement
     */
    public function build() {
        $built = $this->statement->build();
        return new BuiltStatement(sprintf('MAX(%s)', $built->getString()), $built->getParameters());
    }
}