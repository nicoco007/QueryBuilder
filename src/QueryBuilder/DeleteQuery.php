<?php

namespace QueryBuilder;


class DeleteQuery extends Query {
    /** @var bool */
    private $low_priority;

    /** @var bool */
    private $quick;

    /** @var bool */
    private $ignore;

    /** @var ConditionCollection */
    protected $condition_collection;

    /** @var OrderByColumn[] */
    private $order_by = [];

    /** @var int */
    private $limit;

    public function __construct($table_name) {
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
     * @param bool $quick
     * @return $this
     */
    public function setQuick($quick) {
        if (!is_bool($quick))
            throw new \InvalidArgumentException('Expected $quick to be bool, got ' . Util::get_type($quick));

        $this->quick = $quick;

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
     * @param ConditionCollection $condition_collection
     * @return $this
     */
    public function setConditionCollection($condition_collection) {
        if (!($condition_collection instanceof ConditionCollection))
            throw new \InvalidArgumentException('Expected $condition_collection to be ConditionCollection, got ' . Util::get_type($condition_collection));

        $this->condition_collection = $condition_collection;

        return $this;
    }

    /**
     * @param int $limit
     * @return $this
     */
    public function setLimit($limit) {
        if (!is_int($limit))
            throw new \InvalidArgumentException('Expected $limit to be int, got ' . Util::get_type($limit));

        $this->limit = $limit;

        return $this;
    }

    /**
     * @param string $column_name
     * @param int $direction
     * @param string|null $table_name
     * @return $this
     */
    public function addOrder($column_name, $direction = ORDER_ASC, $table_name = null) {
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
     * @return BuiltQuery
     */
    public function build() {
        $builder = new QueryStringBuilder('DELETE');

        if ($this->low_priority)
            $builder->append(' LOW_PRIORITY');

        if ($this->quick)
            $builder->append(' QUICK');

        if ($this->ignore)
            $builder->append(' IGNORE');

        $builder->append(' FROM `%s`', $this->table_name);

        if ($this->condition_collection !== null) {
            $builder->append(' WHERE ');
            $builder->appendBuildable($this->condition_collection);
        }

        if (count($this->order_by) > 0) {
            $builder->append(' ORDER BY ');
            $builder->appendBuildableCollection($this->order_by);
        }

        if ($this->limit > 0) {
            $builder->append(' LIMIT ' . $this->limit);
        }

        return $builder->toBuiltQuery();
    }
}