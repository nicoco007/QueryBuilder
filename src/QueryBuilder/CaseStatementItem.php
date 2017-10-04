<?php

namespace QueryBuilder;


class CaseStatementItem {
    /** @var Condition */
    private $condition;

    /** @var Statement */
    private $value;

    /**
     * @param Condition $condition
     * @param Statement $value
     */
    public function __construct($condition, $value) {
        if (!($condition instanceof Condition))
            throw new \InvalidArgumentException('Expected $condition to be Condition, got ' . Util::get_type($condition));

        if (!($value instanceof Statement))
            throw new \InvalidArgumentException('Expected $value to be Statement, got ' . Util::get_type($value));

        $this->condition = $condition;
        $this->value = $value;
    }

    /**
     * @return Condition
     */
    public function getCondition() {
        return $this->condition;
    }

    /**
     * @return Statement
     */
    public function getValue() {
        return $this->value;
    }
}