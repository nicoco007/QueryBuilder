<?php

namespace QueryBuilder;


class SelectQuery extends Query {
    /** @var OrderByColumn[] */
    private $order_by;

    /** @var Column[] */
    private $columns;

    /**
     * @param string $table_name
     */
    public function __construct($table_name) {
        parent::__construct($table_name);
    }

    public function build() {
        // init vars
        $query = 'SELECT ';
        $params = array();

        // check if we have columns
        if (count($this->columns) > 0) {
            // convert columns array to string array
            $columns_str = array_map(function($column) {
                /** @var Column $column */
                if ($column->getTableName() != null) {
                    return sprintf("`%s`.`%s`", $column->getTableName(), $column->getColumnName());
                } else {
                    return '`' . $column->getColumnName() . '`';
                }
            }, $this->columns);

            // add columns to query, separated by comma
            $query .= implode(', ', $columns_str);
        } else {
            // use wildcard if no columns are specified
            $query .= '*';
        }

        // add from table
        $query .= ' FROM `' . $this->table_name . '`';

        // add where statement
        if ($this->condition_collection !== null && $this->condition_collection->has_elements()) {
            $where = $this->condition_collection->build();
            $query .= ' WHERE ' . $where->getQueryString();
            $params = array_merge($params, $where->getParameters());
        }

        if (!empty($this->order_by)) {
            $query .= ' ORDER BY';

            $order_strings = array();

            foreach ($this->order_by as $order) {
                $order_string = ' `' . $order->getColumnName() . '`';

                switch($order->getDirection()) {
                    case ORDER_ASC:
                        $order_string .= ' ASC';
                        break;
                    case ORDER_DESC:
                        $order_string .= ' DESC';
                        break;
                }

                $order_strings[] = $order_string;
            }

            $query .= implode(', ', $order_strings);
        }

        return new BuiltQuery($query, $params);
    }

    /**
     * @param string|SelectQuery $query Table name or SELECT query
     * @param ConditionCollection|Condition $on Condition(s)
     * @param null $as
     */
    public function join($query, $on, $as = null) {

    }

    /**
     * @param string $column_name
     * @param string|null $table_name
     * @return $this
     */
    public function addColumn($column_name, $table_name = null) {
        $this->columns[] = new Column($column_name, $table_name);

        return $this;
    }

    /**
     * @param string $column_name
     * @param int $order
     * @param string|null $table_name
     * @return $this
     */
    public function addOrderBy($column_name, $order = SORT_ASC, $table_name = null) {
        $this->order_by[] = new OrderByColumn($column_name, $order, $table_name);

        return $this;
    }
}