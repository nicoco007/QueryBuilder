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
     */
    public function __construct($query, $alias) {
        if (!($query instanceof Statement) && !($query instanceof SelectQuery))
            throw new \InvalidArgumentException('Expected $query to be Statement or SelectQuery, got ' . Util::get_type($query));

        if (!is_string($alias))
            throw new \InvalidArgumentException('Expected $alias to be string, got '. Util::get_type($alias));

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
            $string = sprintf('(%s) AS `%s`', $built->getString(), $this->alias);
            $params = array_merge($params, $built->getParameters());
        } elseif ($this->query instanceof Statement) {
            $built = $this->query->build();
            $string = sprintf('%s AS `%s`', $built->getString(), $this->alias);
            $params = array_merge($params, $built->getParameters());
        }

        if ($this->condition_collection !== null) {
            $built = $this->condition_collection->build();
            $string .= ' ON ' . $built->getString();
            $params = array_merge($params, $built->getParameters());
        }

        return new BuiltQuery($string, $params);
    }

    /**
     * @param Statement $statement1
     * @param Statement $statement2
     * @param string $operator
     * @return $this
     */
    public function setCondition($statement1, $statement2, $operator = '=') {
        if (!($statement1 instanceof Statement))
            throw new \InvalidArgumentException('Expected $statement1 to be Statement, got ' . Util::get_type($statement1));

        if (!($statement2 instanceof Statement))
            throw new \InvalidArgumentException('Expected $statement2 to be Statement, got ' . Util::get_type($statement2));

        if (!is_string($operator))
            throw new \InvalidArgumentException('Expected $operator to be string, got ' . Util::get_type($operator));

        $this->condition_collection = new ConditionCollection(OPERATOR_AND, [new Condition($statement1, $statement2, $operator)]);

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