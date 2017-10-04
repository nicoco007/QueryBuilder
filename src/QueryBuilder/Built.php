<?php
/**
 * Created by PhpStorm.
 * User: Nicolas
 * Date: 2017-10-04
 * Time: 13:09
 */

namespace QueryBuilder;


abstract class Built {
    abstract function getString();
    abstract function getParameters();
}