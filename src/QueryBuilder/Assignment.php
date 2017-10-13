<?php

namespace QueryBuilder;


class Assignment implements Buildable {
    /** @var ColumnStatement */
    private $column;

    /** @var Statement */
    private $statement;

    /**
     * Condition constructor.
     * @param ColumnStatement $column
     * @param Statement $statement
     */
    public function __construct($column, $statement) {
        if (!($column instanceof ColumnStatement))
            throw new \InvalidArgumentException('Expected $column to be ColumnStatement, got ' . Util::get_type($column));

        if (!($statement instanceof Statement))
            throw new \InvalidArgumentException('Expected $statement to be Statement, got ' . Util::get_type($statement));

        $this->column = $column;
        $this->statement = $statement;
    }

    /**
     * @return BuiltCondition
     */
    public function build() {
        $column_built = $this->column->build();
        $statement_built = $this->statement->build();

        return new BuiltCondition(
            sprintf("%s = %s", $column_built->getString(), $statement_built->getString()),
            array_merge($column_built->getParameters(), $statement_built->getParameters())
        );
    }

    /**
     * @return Statement
     */
    public function getColumn() {
        return $this->column;
    }

    /**
     * @return Statement
     */
    public function getStatement() {
        return $this->statement;
    }
}