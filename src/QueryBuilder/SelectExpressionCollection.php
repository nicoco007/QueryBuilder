<?php
/**
 * Created by PhpStorm.
 * User: Nicolas
 * Date: 2017-10-12
 * Time: 23:53
 */

namespace QueryBuilder;


class SelectExpressionCollection implements \Countable {
    /**
     * @var SelectExpression[]
     */
    private $expressions = [];

    public function addExpression($statement, $alias = null) {
        if (!($statement instanceof Statement))
            throw new \InvalidArgumentException('Expected $statement to be Statement, got ' . Util::get_type($statement));

        if ($alias !== null && !is_string($alias))
            throw new \InvalidArgumentException('Expected $alias to be string or null, got ' . Util::get_type($alias));

        $this->expressions[] = new SelectExpression($statement, $alias);
    }

    public function build() {
        $strs = [];
        $params = [];

        foreach ($this->expressions as $expression) {
            $built_expression = $expression->build();
            $strs[] = $built_expression->getString();
            $params = array_merge($params, $built_expression->getParameters());
        }

        return new BuiltStatement(implode(', ', $strs), $params);
    }

    public function count() {
        return count($this->expressions);
    }
}