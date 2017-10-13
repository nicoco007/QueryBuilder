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

    /** @var OrderByCollection */
    private $order_by;

    /** @var int */
    private $limit;

    public function __construct($table_name) {
        parent::__construct($table_name);

        $this->order_by = new OrderByCollection();
    }

    /**
     * @param bool $low_priority
     * @return $this
     */
    public function setLowPriority($low_priority) {
        $this->low_priority = $low_priority;

        return $this;
    }

    /**
     * @param bool $quick
     * @return $this
     */
    public function setQuick($quick) {
        $this->quick = $quick;

        return $this;
    }

    /**
     * @param bool $ignore
     * @return $this
     */
    public function setIgnore($ignore) {
        $this->ignore = $ignore;

        return $this;
    }

    /**
     * @param ConditionCollection $condition_collection
     * @return $this
     */
    public function setConditionCollection($condition_collection) {
        $this->condition_collection = $condition_collection;

        return $this;
    }

    /**
     * @param int $limit
     * @return $this
     */
    public function setLimit($limit) {
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
        $this->order_by->addOrder($column_name, $direction, $table_name);

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
            $builder->appendStatement($this->condition_collection->build());
        }

        if (count($this->order_by) > 0) {
            $builder->append(' ORDER BY ');
            $builder->appendStatement($this->order_by->build());
        }

        if ($this->limit > 0) {
            $builder->append(' LIMIT ' . $this->limit);
        }

        return $builder->toBuiltQuery();
    }
}