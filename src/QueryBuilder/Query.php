<?php

namespace QueryBuilder;


abstract class Query {
    /** @var string */
    protected $table_name;

    /** @var string */
    protected $alias;

    /** @var ConditionCollection */
    protected $condition_collection;

    /**
     * @return BuiltQuery
     */
    public abstract function build();

    /**
     * @param string $table_name
     * @param string|null $alias
     */
    public function __construct($table_name, $alias = null) {
        $this->table_name = $table_name;
        $this->alias = $alias;
    }

    /**
     * @param Statement $statement1
     * @param Statement $statement2
     * @param string $operator
     * @return $this
     */
    public function setCondition($statement1, $statement2, $operator = '=') {
        $this->condition_collection = new ConditionCollection(OPERATOR_AND, [new Condition($statement1, $statement2, $operator)]);

        return $this;
    }

    /**
     * @param ConditionCollection $condition_collection
     * @return $this
     */
    public function setConditionCollection($condition_collection) {
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