<?php

namespace QueryBuilder;


class BuiltCondition implements Built {
    /** @var string */
    private $string;

    /** @var mixed[] */
    private $parameters;

    /**
     * BuiltCondition constructor.
     * @param string $string
     * @param mixed[] $parameters
     */
    public function __construct($string, $parameters) {
        if (!is_string($string))
            throw new \InvalidArgumentException('Expected $string to be string, got ' . Util::get_type($string));

        $this->string = $string;
        $this->parameters = $parameters;
    }

    /**
     * @return string
     */
    public function getString() {
        return $this->string;
    }

    /**
     * @return mixed
     */
    public function getParameters() {
        return $this->parameters;
    }
}