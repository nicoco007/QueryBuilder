<?php

namespace QueryBuilder;


abstract class Statement {
    /**
     * @return BuiltQuery
     */
    public abstract function build();
}