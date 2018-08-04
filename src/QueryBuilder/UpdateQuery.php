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
    private $order_by = [];

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
     * @param string $column_name
     * @param int $direction
     * @param string|null $table_name
     * @return $this
     */
    public function addOrderBy($column_name, $direction = ORDER_ASC, $table_name = null) {
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

        $stringBuilder = new QueryStringBuilder('UPDATE');

        if ($this->low_priority)
            $stringBuilder->append(' LOW_PRIORITY');

        if ($this->ignore)
            $stringBuilder->append(' IGNORE');

        $stringBuilder->append(' `%s`', $this->table_name);

        if (!empty($this->assignments)) {
            $stringBuilder->append(' SET ');
            $stringBuilder->appendBuildableCollection($this->assignments);
        }

        if ($this->condition_collection !== null) {
            $stringBuilder->append(' WHERE ');
            $stringBuilder->appendBuildable($this->condition_collection);
        }

        if (!empty($this->order_by)) {
            $stringBuilder->append(' ORDER BY ');
            $stringBuilder->appendBuildableCollection($this->order_by);
        }

        if ($this->limit !== null)
            $stringBuilder->append(' LIMIT ' . $this->limit);

        return $stringBuilder->toBuiltQuery();
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