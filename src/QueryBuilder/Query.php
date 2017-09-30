<?php

namespace QueryBuilder;


abstract class Query {
    /** @var string */
    protected $table_name;

    /** @var ConditionCollection */
    protected $condition_collection;

    /**
     * @return BuiltQuery
     */
    public abstract function build();

    /**
     * @param string $table_name
     */
    public function __construct($table_name) {
        $this->table_name = $table_name;
    }

    /**
     * @param string $column_name
     * @param mixed $value
     * @param string $operator
     * @param string|null $table_name
     * @return $this
     */
    public function setCondition($column_name, $value, $operator = '=', $table_name = null) {
        $this->condition_collection = new ConditionCollection(OPERATOR_AND, [new Condition($column_name, $value, $operator, $table_name)]);

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
}