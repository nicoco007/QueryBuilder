<?php

namespace QueryBuilder;


class Condition {
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
        $this->statement1 = $statement1;
        $this->statement2 = $statement2;
        $this->operator = $operator;
    }

    /**
     * @return BuiltQuery
     */
    public function build() {
        $statement1_built = $this->statement1->build();
        $statement2_built = $this->statement2->build();

        return new BuiltQuery(
            sprintf("%s %s %s", $statement1_built->getQueryString(), $this->operator, $statement2_built->getQueryString()),
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