<?php

namespace QueryBuilder;


class Condition implements Buildable {
    /** @var Statement */
    private $statement1;

    /** @var Statement */
    private $statement2;

    /** @var string */
    private $operator;

    /**
     * Condition constructor.
     * @param Statement $statement1
     * @param Statement $statement2
     * @param string $operator
     */
    public function __construct($statement1, $statement2, $operator = '=') {
        if (!($statement1 instanceof Statement))
            throw new \InvalidArgumentException('Expected $statement1 to be Statement, got ' . Util::get_type($statement1));

        if (!($statement2 instanceof Statement))
            throw new \InvalidArgumentException('Expected $statement2 to be Statement, got ' . Util::get_type($statement2));

        if (!is_string($operator))
            throw new \InvalidArgumentException('Expected $operator to be string, got ' . Util::get_type($operator));

        $this->statement1 = $statement1;
        $this->statement2 = $statement2;
        $this->operator = $operator;
    }

    /**
     * @return BuiltCondition
     */
    public function build() {
        $statement1_built = $this->statement1->build();
        $statement2_built = $this->statement2->build();

        return new BuiltCondition(
            sprintf("%s %s %s", $statement1_built->getString(), $this->operator, $statement2_built->getString()),
            array_merge($statement1_built->getParameters(), $statement2_built->getParameters())
        );
    }

    /**
     * @return Statement
     */
    public function getStatement1() {
        return $this->statement1;
    }

    /**
     * @return Statement
     */
    public function getStatement2() {
        return $this->statement2;
    }

    /**
     * @return string
     */
    public function getOperator() {
        return $this->operator;
    }
}