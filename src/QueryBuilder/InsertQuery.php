<?php

namespace QueryBuilder;


class InsertQuery extends Query {
    /** @var bool */
    private $low_priority = false;

    /** @var bool */
    private $high_priority = false;

    /** @var bool */
    private $ignore = false;

    /** @var Assignment[] */
    private $assignments = [];

    /**
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
     * @throws InvalidQueryException
     */
    public function setLowPriority($low_priority) {
        if (!is_bool($low_priority))
            throw new \InvalidArgumentException('Expected $low_priority to be bool, got ' . Util::get_type($low_priority));

        if ($this->high_priority === true && $low_priority === true)
            throw new InvalidQueryException('Query cannot be both low and high priority');

        $this->low_priority = $low_priority;

        return $this;
    }

    /**
     * @param bool $high_priority
     * @return $this
     * @throws InvalidQueryException
     */
    public function setHighPriority($high_priority) {
        if (!is_bool($high_priority))
            throw new \InvalidArgumentException('Expected $high_priority to be bool, got ' . Util::get_type($high_priority));

        if ($this->low_priority === true && $high_priority === true)
            throw new InvalidQueryException('Query cannot be both high and low priority');

        $this->high_priority = $high_priority;

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
     * @param ColumnStatement $column
     * @param Statement $statement
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
     * @return BuiltQuery
     * @throws InvalidQueryException
     */
    public function build() {
        if (count($this->assignments) === 0)
            throw new InvalidQueryException('You must supply at least one assignment for an INSERT query');

        $builder = new QueryStringBuilder('INSERT');

        if ($this->low_priority)
            $builder->append(' LOW_PRIORITY');

        if ($this->high_priority)
            $builder->append(' HIGH_PRIORITY');

        if ($this->ignore)
            $builder->append(' IGNORE');

        $builder->append(' INTO `%s`', $this->table_name);

        // TODO: PARTITION

        foreach ($this->assignments as $assignment) {
            $builder->append(' SET ');
            $builder->appendStatement($assignment->build());
        }

        // TODO: ON DUPLICATE KEY UPDATE

        return $builder->toBuiltQuery();
    }
}