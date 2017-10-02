<?php
/**
 * Created by PhpStorm.
 * User: Nicolas
 * Date: 2017-09-30
 * Time: 20:07
 */

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