<?php
/**
 * Created by PhpStorm.
 * User: Nicolas
 * Date: 2017-09-30
 * Time: 20:03
 */

namespace QueryBuilder;


class CaseStatement extends Statement {
    /** @var CaseStatementItem[] */
    private $cases;

    /** @var Statement|null */
    private $else_value;

    /**
     * @param Condition $condition
     * @param Statement|string $value
     * @return $this
     */
    public function addCase($condition, $value) {
        $this->cases[] = new CaseStatementItem($condition, $value instanceof Statement ? $value : new StringStatement($value));

        return $this;
    }

    /**
     * @param Condition $statement
     * @return $this
     */
    public function setElseValue($statement) {
        if ($statement instanceof Statement)
            $this->else_value = $statement;
        else
            $this->else_value = new StringStatement($statement);

        return $this;
    }

    /**
     * @return BuiltQuery
     */
    public function build() {
        if (count($this->cases) == 0)
            throw new \InvalidArgumentException('Must have at least one item for CASE statement');

        $params = [];

        $statements = array_map(function($case) use(&$params) {
            /** @var CaseStatementItem $case */
            $built_condition = $case->getCondition()->build();
            $built_value = $case->getValue()->build();

            $params = array_merge($params, $built_condition->getParameters(), $built_value->getParameters());

            return sprintf('WHEN %s THEN %s', $built_condition->getQueryString(), $case->getValue()->build()->getQueryString());
        }, $this->cases);

        return new BuiltQuery(sprintf('CASE %s END', implode(', ', $statements)), $params);
    }
}