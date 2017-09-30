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
            $conditions[] = $built->getString();
            $parameters[] = $built->getParameter();
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
     * @param string $column_name
     * @param mixed $value
     * @param string $operator
     * @param string|null $table_name
     * @return $this
     */
    public function addCondition($column_name, $value, $operator = '=', $table_name = null) {
        $this->conditions[] = new Condition($column_name, $value, $operator, $table_name);

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