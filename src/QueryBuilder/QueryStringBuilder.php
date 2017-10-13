<?php

namespace QueryBuilder;


class QueryStringBuilder {
    /** @var string */
    private $query_string = '';

    /** @var mixed[] */
    private $parameters = [];

    /**
     * QueryStringBuilder constructor.
     * @param string $start_str
     */
    public function __construct($start_str = '') {
        if (!is_string($start_str))
            throw new \InvalidArgumentException('Expected $start_str to be string, got ' . Util::get_type($start_str));

        $this->query_string = $start_str;
    }

    /**
     * @param string $str
     * @param mixed[] $args
     */
    public function append($str, $args = []) {
        if (!is_string($str))
            throw new \InvalidArgumentException('Expected $str to be string, got ' . Util::get_type($str));

        if (count(func_get_args()) > 1)
            $this->query_string .= call_user_func_array('sprintf', func_get_args());
        else
            $this->query_string .= $str;
    }

    /**
     * @param Buildable $buildable
     */
    public function appendBuildable($buildable) {
        if (!($buildable instanceof Buildable))
            throw new \InvalidArgumentException('Expected $buildable to be Buildable, got ' . Util::get_type($buildable));

        $built = $buildable->build();

        $this->query_string .= $built->getString();

        $this->appendParameters($built->getParameters());
    }

    /**
     * @param Buildable[] $buildables
     * @param string $glue
     * @param string $prefix
     * @param string $suffix
     */
    public function appendBuildableCollection($buildables, $glue = ', ', $prefix = '', $suffix = '') {
        if (!Util::instanceof_array($buildables, Buildable::class))
            throw new \InvalidArgumentException('Expected $buildables to be array of Buildable, got ' . Util::get_types_array($buildables));

        $strings = [];

        foreach ($buildables as $buildable) {
            $built = $buildable->build();
            $strings[] = $prefix . $built->getString() . $suffix;
            $this->appendParameters($built->getParameters());
        }

        $this->query_string .= implode($glue, $strings);
    }

    /**
     * @param mixed[] $parameters
     */
    public function appendParameters($parameters) {
        $this->parameters = array_merge($this->parameters, $parameters);
    }

    /**
     * @return BuiltQuery
     */
    public function toBuiltQuery() {
        return new BuiltQuery($this->query_string, $this->parameters);
    }

    /**
     * @return BuiltStatement
     */
    public function toBuiltStatement() {
        return new BuiltStatement($this->query_string, $this->parameters);
    }

    /**
     * @return string
     */
    public function getQueryString() {
        return $this->query_string;
    }

    /**
     * @return mixed[]
     */
    public function getParameters() {
        return $this->parameters;
    }
}