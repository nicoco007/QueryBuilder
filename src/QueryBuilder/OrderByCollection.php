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