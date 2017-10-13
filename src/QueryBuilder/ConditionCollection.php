<?php

namespace QueryBuilder;


class ConditionCollection implements Buildable {
    /** @var int */
    private $operator;

    /** @var Condition[] */
    private $conditions = [];

    /** @var ConditionCollection[] */
    private $children_collections = [];

    /**
     * @param string $operator
     * @param Condition[] $conditions
     * @param ConditionCollection[] $children_collections
     */
    public function __construct($operator, $conditions = [], $children_collections = []) {
        if (!is_string($operator))
            throw new \InvalidArgumentException('Expected $operator to be string, got ' . Util::get_type($operator));

        if (!Util::instanceof_array($conditions, Condition::class))
            throw new \InvalidArgumentException('Expected $conditions to be array of Condition, got ' . Util::get_types_array($conditions));

        if (!Util::instanceof_array($children_collections, ConditionCollection::class))
            throw new \InvalidArgumentException('Expected $children_collections to be array of ConditionCollection, got ' . Util::get_types_array($children_collections));

        $this->operator = $operator;
        $this->conditions = $conditions;
        $this->children_collections = $children_collections;
    }

    /**
     * @return BuiltQuery
     */
    public function build() {
        if (count($this->conditions) == 0 && count($this->children_collections) == 0)
            throw new \InvalidArgumentException('Condition collection must have at least one Condition or child ConditionCollection');

        $conditions = [];
        $parameters = [];

        foreach ($this->conditions as $condition) {
            $built = $condition->build();
            $conditions[] = $built->getString();
            $parameters = array_merge($parameters, $built->getParameters());
        }

        $children = array_map(function($child) {
            /** @var ConditionCollection $child */
            return '(' . $child->build()->getString() . ')';
        }, $this->children_collections);

        $array = array_merge($conditions, $children);

        $query_string = implode(' ' . $this->operator . ' ', $array);

        return new BuiltQuery($query_string, $parameters);
    }

    /**
     * @param Statement $statement1
     * @param Statement $statement2
     * @param string $operator
     * @return $this
     */
    public function addCondition($statement1, $statement2, $operator = '=') {
        if (!($statement1 instanceof Statement))
            throw new \InvalidArgumentException('Expected $statement1 to be Statement, got ' . Util::get_type($statement1));

        if (!($statement2 instanceof Statement))
            throw new \InvalidArgumentException('Expected $statement2 to be Statement, got ' . Util::get_type($statement2));

        if (!is_string($operator))
            throw new \InvalidArgumentException('Expected $operator to be string, got ' . Util::get_type($operator));

        $this->conditions[] = new Condition($statement1, $statement2, $operator);

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

    /**
     * @return ConditionCollection[]
     */
    public function getChildrenCollections() {
        return $this->children_collections;
    }
}