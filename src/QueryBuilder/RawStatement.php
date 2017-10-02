<?php
/**
 * Created by PhpStorm.
 * User: Nicolas
 * Date: 2017-09-30
 * Time: 19:45
 */

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
     * @return BuiltQuery
     */
    public function build() {
        return new BuiltQuery('?', [$this->value]);
    }

    /**
     * @return mixed
     */
    public function getValue() {
        return $this->value;
    }
}