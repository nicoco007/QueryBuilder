<?php

namespace QueryBuilder;


class OrderByColumn {
    /** @var string */
    private $column_name;

    /** @var int */
    private $direction;

    /** @var string|null */
    private $table_name;

    /**
     * @param string $column_name
     * @param int $direction
     * @param string|null $table_name
     */
    public function __construct($column_name, $direction = ORDER_ASC, $table_name = null) {
        if (!is_string($column_name))
            throw new \InvalidArgumentException('Expected $column_name to be string, got ' . Util::get_type($column_name));

        if (!is_int($direction))
            throw new \InvalidArgumentException('Expected $direction to be int, got ' . Util::get_type($direction));

        if ($table_name !== null && !is_string($table_name))
            throw new \InvalidArgumentException('Expected $table_name to be string or null, got ' . Util::get_type($table_name));

        $this->column_name = $column_name;
        $this->direction = $direction;
        $this->table_name = $table_name;
    }

    /**
     * @return string
     */
    public function getColumnName() {
        return $this->column_name;
    }

    /**
     * @return int
     */
    public function getDirection() {
        return $this->direction;
    }

    /**
     * @return string|null
     */
    public function getTableName() {
        return $this->table_name;
    }
}