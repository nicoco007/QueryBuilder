<?php

namespace QueryBuilder;

const ORDER_ASC = 1;
const ORDER_DESC = 2;


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
        $this->column_name = $column_name;
        $this->direction = $direction;
        $this->table_name = $table_name;
    }

    public function getColumnName() {
        return $this->column_name;
    }

    public function getDirection() {
        return $this->direction;
    }

    public function getTableName() {
        return $this->table_name;
    }
}