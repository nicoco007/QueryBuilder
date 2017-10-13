<?php

namespace QueryBuilder;


class SelectQuery extends Query {
    /** @var OrderByCollection */
    private $order_by;

    /** @var SelectExpressionCollection */
    private $expressions;

    /** @var Join[] */
    private $joins = [];

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

        $this->expressions = new SelectExpressionCollection();
    }

    public function build() {
        $builder = new QueryStringBuilder('SELECT ');

        // check if we have columns
        if (count($this->expressions) > 0) {
            $builder->appendStatement($this->expressions->build());
        } else {
            // use wildcard if no columns are specified
            $builder->append('*');
        }

        // add from table
        $builder->append(' FROM `%s`', $this->table_name);

        if ($this->group_by !== null) {
            $builder->append(' GROUP BY ');
            $builder->appendStatement($this->group_by->build());
        }

        if ($this->alias !== null)
            $builder->append(' AS `%s`', $this->alias);

        if ($this->joins !== null && count($this->joins) > 0) {
            foreach ($this->joins as $join) {
                $builder->append(' JOIN ');
                $builder->appendStatement($join->build());
            }
        }

        // add where statement
        if ($this->condition_collection !== null && $this->condition_collection->has_elements()) {
            $builder->append(' WHERE ');
            $builder->appendStatement($this->condition_collection->build());
        }

        if (count($this->order_by) > 0) {
            $builder->append(' ORDER BY ');
            $builder->appendStatement($this->order_by->build());
        }

        return $builder->toBuiltQuery();
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

        $this->expressions->addExpression($statement, $alias);

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

        $this->order_by->addOrder($column_name, $direction, $table_name);

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