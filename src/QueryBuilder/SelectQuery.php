<?php

namespace QueryBuilder;


class SelectQuery extends Query {
    /** @var OrderByColumn[] */
    private $order_by;

    /** @var QueryStatement[] */
    private $statements;

    /** @var Join[] */
    private $joins;

    /** @var Statement */
    private $group_by;

    /**
     * @param string $table_name
     * @param string|null $alias
     */
    public function __construct($table_name, $alias = null) {
        parent::__construct($table_name, $alias);
    }

    public function build() {
        // init vars
        $query = 'SELECT ';
        $params = array();

        // check if we have columns
        if (count($this->statements) > 0) {
            $statements = [];

            foreach ($this->statements as $statement) {
                $built_statement = $statement->getStatement()->build();
                $params = array_merge($params, $built_statement->getParameters());

                if ($statement->getAlias() !== null) {
                    $statements[] = sprintf('%s AS %s', $built_statement->getString(), $statement->getAlias());
                } else {
                    $statements[] = $built_statement->getString();
                }
            }

            $query .= implode(', ', $statements);
        } else {
            // use wildcard if no columns are specified
            $query .= '*';
        }

        // add from table
        $query .= ' FROM `' . $this->table_name . '`';

        if ($this->group_by !== null) {
            $group_by_built = $this->group_by->build();
            $query .= ' GROUP BY ' . $group_by_built->getString();
            $params = array_merge($params, $group_by_built->getParameters());
        }

        if ($this->alias !== null) {
            $query .= sprintf(' AS `%s`', $this->alias);
        }

        if ($this->joins !== null && count($this->joins) > 0) {
            foreach ($this->joins as $join) {
                $built_join = $join->build();
                $query .= ' JOIN ' . $built_join->getQueryString();
                $params = array_merge($params, $built_join->getParameters());
            }
        }

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
     * @param Statement $statement
     * @param string|null $alias
     * @return $this
     */
    public function addStatement($statement, $alias = null) {
        $this->statements[] = new QueryStatement($statement, $alias);

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

    /**
     * @param Join $join
     * @return $this
     */
    public function addJoin($join) {
        $this->joins[] = $join;

        return $this;
    }

    /**
     * @param Statement $group_by
     * @return $this
     */
    public function setGroupBy($group_by) {
        $this->group_by = $group_by;

        return $this;
    }
}