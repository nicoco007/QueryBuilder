<?php

namespace QueryBuilder;


class SelectQuery extends Query {
    /** @var OrderByColumn[] */
    private $order_by;

    /** @var SelectExpression[] */
    private $expressions;

    /** @var Join[] */
    private $joins = [];

    /** @var Statement */
    private $group_by;

    /**
     * @param string $table_name
     */
    public function __construct($table_name) {
        if (!is_string($table_name))
            throw new \InvalidArgumentException('Expected $table_name to be string, got ' . Util::get_type($table_name));

        parent::__construct($table_name);
    }

    public function build() {
        $builder = new QueryStringBuilder('SELECT ');

        // check if we have columns
        if (count($this->expressions) > 0) {
            $builder->appendBuildableCollection($this->expressions);
        } else {
            // use wildcard if no columns are specified
            $builder->append('*');
        }

        // add from table
        $builder->append(' FROM `%s`', $this->table_name);

        if ($this->group_by !== null) {
            $builder->append(' GROUP BY ');
            $builder->appendBuildable($this->group_by);
        }

        if ($this->alias !== null)
            $builder->append(' AS `%s`', $this->alias);

        if ($this->joins !== null && count($this->joins) > 0) {
            foreach ($this->joins as $join) {
                $builder->append(' JOIN ');
                $builder->appendBuildable($join);
            }
        }

        // add where statement
        if ($this->condition_collection !== null) {
            $builder->append(' WHERE ');
            $builder->appendBuildable($this->condition_collection);
        }

        if (count($this->order_by) > 0) {
            $builder->append(' ORDER BY ');
            $builder->appendBuildableCollection($this->order_by);
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

        $this->expressions[] = new SelectExpression($statement, $alias);

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