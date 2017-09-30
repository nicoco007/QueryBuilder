<?php

namespace QueryBuilder;


class Condition {
    /** @var string */
    private $column_name;

    /** @var string */
    private $operator;

    /** @var mixed */
    private $value;

    /** @var null|string */
    private $table_name;

    /**
     * Condition constructor.
     * @param string $column_name
     * @param mixed $value
     * @param string $operator
     * @param string|null $table_name
     */
    public function __construct($column_name, $value, $operator = '=', $table_name = null) {
        $this->column_name = $column_name;
        $this->operator = $operator;
        $this->value = $value;
        $this->table_name = $table_name;
    }

    /**
     * @return BuiltCondition
     */
    public function build() {
        if ($this->table_name !== null)
            return new BuiltCondition(sprintf("`%s`.`%s` %s ?", $this->table_name, $this->column_name, $this->operator), $this->value);
        else
            return new BuiltCondition(sprintf("`%s` %s ?", $this->column_name, $this->operator), $this->value);
    }

    /**
     * @return string
     */
    public function getColumnName() {
        return $this->column_name;
    }

    /**
     * @return string
     */
    public function getOperator() {
        return $this->operator;
    }

    /**
     * @return mixed
     */
    public function getValue() {
        return $this->value;
    }

    /**
     * @return null|string
     */
    public function getTableName() {
        return $this->table_name;
    }
}