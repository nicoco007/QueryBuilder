<?php
/**
 * Created by PhpStorm.
 * User: Nicolas
 * Date: 2017-09-30
 * Time: 20:11
 */

namespace QueryBuilder;


class ColumnStatement extends Statement {
    private $column_name;
    private $table_name;

    public function __construct($column_name, $table_name = null) {
        $this->column_name = $column_name;
        $this->table_name = $table_name;
    }

    public function build() {
        if ($this->table_name)
            return new BuiltQuery(sprintf('`%s`.`%s`', $this->table_name, $this->column_name), []);
        else
            return new BuiltQuery(sprintf('`%s`', $this->column_name), []);
    }
}