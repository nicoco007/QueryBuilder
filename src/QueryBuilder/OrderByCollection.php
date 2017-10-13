<?php
/**
 * Created by PhpStorm.
 * User: Nicolas
 * Date: 2017-10-10
 * Time: 13:29
 */

namespace QueryBuilder;


class OrderByCollection implements \Countable {
    /** @var OrderByColumn[] */
    private $orders = [];

    public function addOrder($column_name, $direction = ORDER_ASC, $table_name = null) {
        if (!is_string($column_name))
            throw new \InvalidArgumentException('Expected $column_name to be string, got ' . Util::get_type($column_name));

        if (!is_int($direction))
            throw new \InvalidArgumentException('Expected $direction to be int, got ' . Util::get_type($direction));

        if ($table_name !== null && !is_string($table_name))
            throw new \InvalidArgumentException('Expected $table_name to be string or null, got ' . Util::get_type($table_name));

        $this->orders[] = new OrderByColumn($column_name, $direction, $table_name);
    }

    /**
     * @return BuiltStatement
     */
    public function build() {
        $order_strings = [];
        $parameters = [];

        foreach ($this->orders as $order) {
            $built_order = $order->build();
            $order_strings[] = $built_order->getString();
            $parameters = array_merge($parameters, $built_order->getParameters());
        }

        return new BuiltStatement(implode(', ', $order_strings), $parameters);
    }

    public function count() {
        return count($this->orders);
    }
}