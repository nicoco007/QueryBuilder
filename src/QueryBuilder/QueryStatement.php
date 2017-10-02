<?php
/**
 * Created by PhpStorm.
 * User: Nicolas
 * Date: 2017-10-02
 * Time: 00:25
 */

namespace QueryBuilder;


class QueryStatement {
    /** @var Statement */
    private $statement;

    /** @var string|null */
    private $alias;

    /**
     * QueryStatement constructor.
     * @param Statement $statement
     * @param string|null $alias
     */
    public function __construct($statement, $alias = null) {
        $this->statement = $statement;
        $this->alias = $alias;
    }

    /**
     * @return Statement
     */
    public function getStatement() {
        return $this->statement;
    }

    /**
     * @return null|string
     */
    public function getAlias() {
        return $this->alias;
    }
}