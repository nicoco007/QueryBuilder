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

    /**
     * ColumnStatement constructor.
     * @param string $column_name
     * @param string|null $table_name
     */
    public function __construct($column_name, $table_name = null) {
        if (!is_string($column_name))
            throw new \InvalidArgumentException('Expected $column_name to be string, got ' . Util::get_type($column_name));

        if ($table_name !== null && !is_string($table_name))
            throw new \InvalidArgumentException('Expected $table_name to be string, got ' . Util::get_type($table_name));

        $this->column_name = $column_name;
        $this->table_name = $table_name;
    }

    /**
     * @return BuiltStatement
     */
    public function build() {
        if ($this->table_name)
            return new BuiltStatement(sprintf('`%s`.`%s`', $this->table_name, $this->column_name), []);
        else
            return new BuiltStatement(sprintf('`%s`', $this->column_name), []);
    }
}