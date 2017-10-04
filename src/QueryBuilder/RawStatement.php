<?php

namespace QueryBuilder;


class RawStatement extends Statement {
    /** @var mixed */
    private $value;

    /**
     * @param mixed $value
     */
    public function __construct($value) {
        $this->value = $value;
    }

    /**
     * @return BuiltStatement
     */
    public function build() {
        return new BuiltStatement('?', [$this->value]);
    }

    /**
     * @return mixed
     */
    public function getValue() {
        return $this->value;
    }
}