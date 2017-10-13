<?php

namespace QueryBuilder;


abstract class Statement implements Buildable {
    /**
     * @return BuiltStatement
     */
    public abstract function build();
}