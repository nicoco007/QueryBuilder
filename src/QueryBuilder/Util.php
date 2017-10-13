<?php

namespace QueryBuilder;


class Util {
    /**
     * Get the class (if it's an object) or the type of the specified variable.
     * @param mixed $var
     * @return string
     */
    public static function get_type($var) {
        if (is_object($var))
            return get_class($var);
        else
            return gettype($var);
    }

    /**
     * Check if the array elements are instances of the specified class(es).
     * @param array $array
     * @param string|string[] $classes
     * @return bool
     */
    public static function instanceof_array($array, $classes) {
        if (!is_array($classes))
            $classes = [$classes];

        foreach ($array as $value) {
            $found = false;

            foreach ($classes as $class)
                if ($value instanceof $class)
                    $found = true;

            if (!$found)
                return false;
        }

        return true;
    }

    /**
     * Get all classes of the elements in an array.
     * @param array $array
     * @return string
     */
    public static function get_types_array($array) {
        $classes = [];

        foreach ($array as $value)
            if (!in_array(self::get_type($value), $classes))
                $classes[] = self::get_type($value);

        return sprintf('[%s]', implode(', ', $classes));
    }
}