<?php
/**
 * Created by PhpStorm.
 * User: Nicolas
 * Date: 2017-10-07
 * Time: 16:46
 */

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
     * @param Built $built
     */
    public function appendStatement($built) {
        if (!($built instanceof Built))
            throw new \InvalidArgumentException('Expected $built to be Built, got ' . Util::get_type($built));

        $this->query_string .= $built->getString();

        $this->parameters = array_merge($this->parameters, $built->getParameters());
    }

    public function toBuiltQuery() {
        return new BuiltQuery($this->query_string, $this->parameters);
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