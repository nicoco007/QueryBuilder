<?php

namespace QueryBuilder;


abstract class Statement {
    /**
     * @return BuiltStatement
     */
    public abstract function build();
}