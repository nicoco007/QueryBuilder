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
        if (!is_string($table_name))
            throw new \InvalidArgumentException('Expected $table_name to be string, got ' . Util::get_type($table_name));

        if ($alias !== null && !is_string($alias))
            throw new \InvalidArgumentException('Expected $alias to be string or null, got ' . Util::get_type($alias));

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
                $query .= ' JOIN ' . $built_join->getString();
                $params = array_merge($params, $built_join->getParameters());
            }
        }

        // add where statement
        if ($this->condition_collection !== null && $this->condition_collection->has_elements()) {
            $where = $this->condition_collection->build();
            $query .= ' WHERE ' . $where->getString();
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
        if (!($statement instanceof Statement))
            throw new \InvalidArgumentException('Expected $statement to be Statement, got ' . Util::get_type($statement));

        if ($alias !== null && !is_string($alias))
            throw new \InvalidArgumentException('Expected $alias to be string or null, got ' . Util::get_type($alias));

        $this->statements[] = new QueryStatement($statement, $alias);

        return $this;
    }

    /**
     * @param string $column_name
     * @param int $direction
     * @param string|null $table_name
     * @return $this
     */
    public function addOrderBy($column_name, $direction = SORT_ASC, $table_name = null) {
        if (!is_string($column_name))
            throw new \InvalidArgumentException('Expected $column_name to be string, got ' . Util::get_type($column_name));

        if (!is_int($direction))
            throw new \InvalidArgumentException('Expected $direction to be int, got ' . Util::get_type($direction));

        if ($table_name !== null && !is_string($table_name))
            throw new \InvalidArgumentException('Expected $table_name to be string or null, got ' . Util::get_type($table_name));

        $this->order_by[] = new OrderByColumn($column_name, $direction, $table_name);

        return $this;
    }

    /**
     * @param Join $join
     * @return $this
     */
    public function addJoin($join) {
        if (!($join instanceof Join))
            throw new \InvalidArgumentException('Expected $join to be Join, got ' . Util::get_type($join));

        $this->joins[] = $join;

        return $this;
    }

    /**
     * @param Statement $group_by
     * @return $this
     */
    public function setGroupBy($group_by) {
        if (!($group_by instanceof Statement))
            throw new \InvalidArgumentException('Expected $group_by to be Statement, got ' . Util::get_type($group_by));

        $this->group_by = $group_by;

        return $this;
    }
}