<?php

namespace QueryBuilder;


class Join {
    /** @var Statement|SelectQuery */
    private $query;

    /** @var string */
    private $alias;

    /**
     * @var ConditionCollection
     */
    private $condition_collection;

    /**
     * @param Statement|SelectQuery $query
     * @param string $alias
     * @internal param string $group_by
     */
    public function __construct($query, $alias) {
        $this->query = $query;
        $this->alias = $alias;
    }

    /**
     * @return BuiltQuery
     */
    public function build() {
        $string = '';
        $params = [];

        if ($this->query instanceof SelectQuery) {
            $built = $this->query->build();
            $string = sprintf('(%s) AS `%s`', $built->getQueryString(), $this->alias);
            $params = array_merge($params, $built->getParameters());
        } elseif ($this->query instanceof Statement) {
            $built = $this->query->build();
            $string = sprintf('%s AS `%s`', $built->getQueryString(), $this->alias);
            $params = array_merge($params, $built->getParameters());
        }

        if ($this->condition_collection !== null) {
            $built = $this->condition_collection->build();
            $string .= ' ON ' . $built->getQueryString();
            $params = array_merge($params, $built->getParameters());
        }

        return new BuiltQuery($string, $params);
    }

    /**
     * @param string $column_name
     * @param mixed $value
     * @param string $operator
     * @param string|null $table_name
     * @return $this
     */
    public function setCondition($column_name, $value, $operator = '=', $table_name = null) {
        $this->condition_collection = new ConditionCollection(OPERATOR_AND, [new Condition($column_name, $value, $operator, $table_name)]);

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
     * @return string
     */
    public function getTableName() {
        return $this->table_name;
    }

    /**
     * @return SelectQuery|string
     */
    public function getQuery() {
        return $this->query;
    }

    /**
     * @return string
     */
    public function getAlias() {
        return $this->alias;
    }
}