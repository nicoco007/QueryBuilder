<?php

namespace QueryBuilder;


class UpdateQuery extends Query {
    /**
     * @var Assignment[]
     */
    private $assignments = [];

    /**
     * @var bool
     */
    private $low_priority;

    /**
     * @var bool
     */
    private $ignore;

    /**
     * @var OrderByColumn[]
     */
    private $order_by;

    /**
     * @var int
     */
    private $limit;

    /**
     * @var bool
     */
    private $allow_unsafe_update;

    /**
     * UpdateQuery constructor.
     * @param string $table_name
     */

    public function __construct($table_name) {
        if (!is_string($table_name))
            throw new \InvalidArgumentException('Expected $table_name to be string, got ' . Util::get_type($table_name));

        parent::__construct($table_name);
    }

    /**
     * @param bool $low_priority
     * @return $this
     */
    public function setLowPriority($low_priority) {
        if (!is_bool($low_priority))
            throw new \InvalidArgumentException('Expected $low_priority to be bool, got ' . Util::get_type($low_priority));

        $this->low_priority = $low_priority;

        return $this;
    }

    /**
     * @param bool $ignore
     * @return $this
     */
    public function setIgnore($ignore) {
        if (!is_bool($ignore))
            throw new \InvalidArgumentException('Expected $ignore to be bool, got ' . Util::get_type($ignore));

        $this->ignore = $ignore;

        return $this;
    }

    /**
     * @param int $row_count
     * @return $this
     */
    public function setLimit($row_count) {
        if (!is_int($row_count))
            throw new \InvalidArgumentException('Expected $row_count to be int, got ' . Util::get_type($row_count));

        $this->limit = $row_count;

        return $this;
    }

    /**
     * @param bool $allow_unsafe_update
     * @return $this
     */
    public function setAllowUnsafeUpdate($allow_unsafe_update) {
        if (!is_bool($allow_unsafe_update))
            throw new \InvalidArgumentException('Expected $low_priority to be bool, got ' . Util::get_type($allow_unsafe_update));

        $this->allow_unsafe_update = $allow_unsafe_update;

        return $this;
    }

    /**
     * @return BuiltQuery
     * @throws UnsafeUpdateException
     */
    public function build() {
        if ($this->condition_collection == null && !$this->allow_unsafe_update) {
            throw new UnsafeUpdateException('You are attempting to create an UPDATE query with no WHERE clause. Please use setAllowUnsafeUpdate if you are sure you want to do this.');
        }

        $query_string = 'UPDATE';
        $params = [];

        if ($this->low_priority)
            $query_string .= ' LOW_PRIORITY';

        if ($this->ignore)
            $query_string .= ' IGNORE';

        $query_string .= sprintf(' `%s`', $this->table_name);

        $assignments_built = array_map(function($assignment) use(&$params) {
            /** @var $assignment Assignment */
            $built_assignment = $assignment->build();
            $params = array_merge($params, $built_assignment->getParameters());
            return 'SET ' . $built_assignment->getString();
        }, $this->assignments);

        $query_string .= ' ' . implode(', ', $assignments_built);

        if ($this->condition_collection !== null) {
            $built_condition_collection = $this->condition_collection->build();
            $query_string .= ' WHERE ' . $built_condition_collection->getString();
            $params = array_merge($params, $built_condition_collection->getParameters());
        }

        if (!empty($this->order_by)) {
            $query_string .= ' ORDER BY';

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

            $query_string .= implode(', ', $order_strings);
        }

        if ($this->limit !== null) {
            $query_string .= ' LIMIT ' . $this->limit;
        }

        return new BuiltQuery($query_string, $params);
    }

    /**
     * @param ColumnStatement $column
     * @param $statement
     * @return $this
     */
    public function addAssignment($column, $statement) {
        if (!($column instanceof ColumnStatement))
            throw new \InvalidArgumentException('Expected $column to be ColumnStatement, got ' . Util::get_type($column));

        if (!($statement instanceof Statement))
            throw new \InvalidArgumentException('Expected $statement to be Statement, got ' . Util::get_type($statement));

        $this->assignments[] = new Assignment($column, $statement);

        return $this;
    }

    /**
     * @return Assignment[]
     */
    public function getAssignments() {
        return $this->assignments;
    }
}