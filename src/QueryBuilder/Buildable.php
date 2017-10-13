<?php
/**
 * Created by PhpStorm.
 * User: Nicolas
 * Date: 2017-10-13
 * Time: 00:20
 */

namespace QueryBuilder;


interface Buildable {
    /**
     * @return Built
     */
    public function build();
}