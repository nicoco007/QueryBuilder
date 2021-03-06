<?php

namespace QueryBuilder;


abstract class Query implements Buildable {
    /** @var string */
    protected $table_name;

    /** @var string */
    protected $alias;

    /** @var ConditionCollection */
    protected $condition_collection;

    /**
     * @param string $table_name
     */
    public function __construct($table_name) {
        if (!is_string($table_name))
            throw new \InvalidArgumentException('Expected $table_name to be string, got ' . Util::get_type($table_name));

        $this->table_name = $table_name;
    }

    /**
     * @param string $alias
     * @return $this
     */
    public function setAlias($alias) {
        if (!is_string($alias))
            throw new \InvalidArgumentException('Expected $alias to be string or null, got ' . Util::get_type($alias));

        $this->alias = $alias;

        return $this;
    }

    /**
     * @param Statement $statement1
     * @param Statement $statement2
     * @param string $operator
     * @return $this
     */
    public function setCondition($statement1, $statement2, $operator = '=') {
        if (!($statement1 instanceof Statement))
            throw new \InvalidArgumentException('Expected $statement1 to be Statement, got ' . Util::get_type($statement1));

        if (!($statement2 instanceof Statement))
            throw new \InvalidArgumentException('Expected $statement2 to be Statement, got ' . Util::get_type($statement2));

        if (!is_string($operator))
            throw new \InvalidArgumentException('Expected $operator to be string, got ' . Util::get_type($operator));

        $this->condition_collection = new ConditionCollection(OPERATOR_AND, [new Condition($statement1, $statement2, $operator)]);

        return $this;
    }

    /**
     * @param ConditionCollection $condition_collection
     * @return $this
     */
    public function setConditionCollection($condition_collection) {
        if (!($condition_collection instanceof ConditionCollection))
            throw new \InvalidArgumentException('Expected $condition_collection to be ConditionCollection, got ' . Util::get_type($condition_collection));

        $this->condition_collection = $condition_collection;

        return $this;
    }

    /**
     * @return string
     */
    public function getTableName() {
        return $this->table_name;
    }

    /**
     * @return string
     */
    public function getAlias() {
        return $this->alias;
    }

    /**
     * @return ConditionCollection
     */
    public function getConditionCollection() {
        return $this->condition_collection;
    }
}