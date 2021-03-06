<?php

namespace QueryBuilder;


class BuiltQuery implements Built {
    /** @var string */
    private $query_string;

    /** @var mixed[] */
    private $parameters;

    /**
     * BuiltQuery constructor.
     * @param string $query_string
     * @param mixed[] $parameters
     */
    public function __construct($query_string, $parameters) {
        if (!is_string($query_string))
            throw new \InvalidArgumentException('Expected $query_string to be string, got ' . Util::get_type($query_string));

        if (!is_array($parameters))
            throw new \InvalidArgumentException('Expected $parameters to be array, got ' . Util::get_type($string));

        $this->query_string = $query_string;
        $this->parameters = $parameters;
    }

    /**
     * @return string
     */
    public function getString() {
        return $this->query_string;
    }

    /**
     * @return mixed[]
     */
    public function getParameters() {
        return $this->parameters;
    }
}