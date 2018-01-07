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
        $builder = new QueryStringBuilder();

        if (count($this->conditions) == 0 && count($this->children_collections) == 0) {
            $builder->append('TRUE');
            return $builder->toBuiltQuery();
        }

        $builder->appendBuildableCollection($this->conditions, " $this->operator ");

        if (count($this->conditions) > 0 && count($this->children_collections) > 0)
            $builder->append(sprintf(' %s ', $this->operator));

        $builder->appendBuildableCollection($this->children_collections, " $this->operator ", '(', ')');

        return $builder->toBuiltQuery();
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
     * @param ConditionCollection $condition_collection
     * @return $this
     */
    public function addChild($condition_collection) {
        if (!($condition_collection instanceof ConditionCollection))
            throw new \InvalidArgumentException('Expected $condition_collection to be ConditionCollection, got ' . Util::get_type($condition_collection));

        $this->children_collections[] = $condition_collection;

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