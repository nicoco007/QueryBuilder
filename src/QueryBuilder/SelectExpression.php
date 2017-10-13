<?php

namespace QueryBuilder;

class SelectExpression {
    /** @var Statement */
    private $statement;

    /** @var string|null */
    private $alias;

    /**
     * SelectExpression constructor.
     * @param Statement $statement
     * @param string|null $alias
     */
    public function __construct($statement, $alias = null) {
        if (!($statement instanceof Statement))
            throw new \InvalidArgumentException('Expected $statement to be Statement, got ' . Util::get_type($statement));

        if ($alias !== null && !is_string($alias))
            throw new \InvalidArgumentException('Expected $alias to be string or null, got ' . Util::get_type($alias));

        $this->statement = $statement;
        $this->alias = $alias;
    }

    /**
     * @return BuiltStatement
     */
    public function build() {
        if ($this->alias !== null) {
            $built_statement = $this->getStatement()->build();
            return new BuiltStatement(sprintf('%s AS %s', $built_statement->getString(), $this->getAlias()), $built_statement->getParameters());
        } else {
            return $this->getStatement()->build();
        }
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