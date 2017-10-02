<?php

namespace QueryBuilder;


class ConditionCollection {
    /** @var int */
    private $operator;

    /** @var Condition[] */
    private $conditions;

    /** @var ConditionCollection[] */
    private $children_collections;

    /**
     * @param int $operator
     * @param Condition[] $conditions
     * @param ConditionCollection[] $children_collections
     */
    public function __construct($operator, $conditions = [], $children_collections = []) {
        $this->operator = $operator;
        $this->conditions = $conditions;
        $this->children_collections = $children_collections;
    }

    /**
     * @return BuiltQuery
     */
    public function build() {
        $conditions = [];
        $parameters = [];

        foreach ($this->conditions as $condition) {
            $built = $condition->build();
            $conditions[] = $built->getQueryString();
            $parameters = array_merge($parameters, $built->getParameters());
        }

        $children = array_map(function($child) {
            /** @var ConditionCollection $child */
            return '(' . $child->build()->getQueryString() . ')';
        }, $this->children_collections);

        $array = array_merge($conditions, $children);

        $query_string = implode(' ' . $this->operator . ' ', $array);

        return new BuiltQuery($query_string, $parameters);
    }

    public function has_elements() {
        return count($this->conditions) > 0 || count($this->children_collections) > 0;
    }

    /**
     * @param Statement $statement1
     * @param Statement $statement2
     * @param string $operator
     * @return $this
     */
    public function addCondition($statement1, $statement2, $operator = '=') {
        $this->conditions[] = new ConditionCollection(OPERATOR_AND, [new Condition($statement1, $statement2, $operator)]);

        return $this;
    }

    /**
     * @return int
     */
    public function getOperator() {
        return $this->operator;
    }

    /**
     * @return Condition[]
     */
    public function getConditions() {
        return $this->conditions;
    }
}