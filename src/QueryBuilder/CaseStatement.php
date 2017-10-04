<?php

namespace QueryBuilder;


class CaseStatement extends Statement {
    /** @var CaseStatementItem[] */
    private $cases;

    /** @var Statement|null */
    private $else_value;

    /**
     * @param Condition $condition
     * @param Statement|mixed $value
     * @return $this
     */
    public function addCase($condition, $value) {
        if (!($condition instanceof Condition))
            throw new \InvalidArgumentException('Expected $condition to be Condition, got ' . Util::get_type($condition));

        $this->cases[] = new CaseStatementItem($condition, $value instanceof Statement ? $value : new RawStatement($value));

        return $this;
    }

    /**
     * @param Statement|mixed $statement
     * @return $this
     */
    public function setElseValue($statement) {
        if ($statement instanceof Statement)
            $this->else_value = $statement;
        else
            $this->else_value = new RawStatement($statement);

        return $this;
    }

    /**
     * @return BuiltStatement
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

            return sprintf('WHEN %s THEN %s', $built_condition->getString(), $case->getValue()->build()->getString());
        }, $this->cases);

        return new BuiltStatement(sprintf('CASE %s END', implode(', ', $statements)), $params);
    }
}