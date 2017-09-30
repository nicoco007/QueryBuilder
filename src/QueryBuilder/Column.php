<?php

namespace QueryBuilder;


class Column {
    /** @var string */
    private $column_name;

    /** @var string|null */
    private $table_name;

    /**
     * @param string $column_name
     * @param string|null $table_name
     */
    public function __construct($column_name, $table_name = null) {
        $this->column_name = $column_name;
        $this->table_name = $table_name;
    }

    /**
     * @return string
     */
    public function getColumnName() {
        return $this->column_name;
    }

    /**
     * @return null|string
     */
    public function getTableName() {
        return $this->table_name;
    }
}