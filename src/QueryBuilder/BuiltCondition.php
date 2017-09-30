<?php

namespace QueryBuilder;


class BuiltCondition {
    /** @var string */
    private $string;

    /** @var mixed */
    private $parameter;

    /**
     * BuiltCondition constructor.
     * @param string $string
     * @param mixed $parameter
     */
    public function __construct($string, $parameter) {
        $this->string = $string;
        $this->parameter = $parameter;
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
    public function getParameter() {
        return $this->parameter;
    }
}