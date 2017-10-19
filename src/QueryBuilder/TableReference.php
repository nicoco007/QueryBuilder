<?php
/**
 * Created by PhpStorm.
 * User: Nicolas
 * Date: 2017-10-18
 * Time: 21:10
 */

namespace QueryBuilder;


class TableReference extends Statement {
    /** @var string */
    private $table_name;

    /**
     * TableReference constructor.
     * @param string $table_name
     */
    public function __construct($table_name) {
        if (!is_string($table_name))
            throw new \InvalidArgumentException('Expected $table_name to be string, got ' . Util::get_type($table_name));

        $this->table_name = $table_name;
    }

    /**
     * @return string
     */
    public function getTableName() {
        return $this->table_name;
    }

    /**
     * @return Built
     */
    public function build() {
        return new BuiltStatement(sprintf('`%s`', $this->table_name));
    }
}