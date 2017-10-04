<?php
/**
 * Created by PhpStorm.
 * User: Nicolas
 * Date: 2017-10-04
 * Time: 12:39
 */

namespace QueryBuilder;


class UpdateQuery extends Query {
    /**
     * @var Assignment[]
     */
    private $assignments;

    /**
     * @var bool
     */
    private $low_priority;

    /**
     * @var bool
     */
    private $ignore;

    /**
     * @var OrderByColumn[]
     */
    private $order_by;

    /**
     * @var int
     */
    private $limit;

    public function __construct($table_name, $low_priority = false, $ignore = false) {
        parent::__construct($table_name);

        $this->low_priority = $low_priority;
        $this->ignore = $ignore;
    }

    /**
     * @param int $row_count
     */
    public function setLimit($row_count) {
        $this->limit = $row_count;
    }

    /**
     * @return BuiltQuery
     */
    public function build() {
        $query_string = 'UPDATE';
        $params = [];

        if ($this->low_priority)
            $query_string .= ' LOW_PRIORITY';

        if ($this->ignore)
            $query_string .= ' IGNORE';

        $query_string .= ' ' . $this->table_name;

        $assignments_built = array_map(function($assignment) use(&$params) {
            /** @var $assignment Assignment */
            $built_assignment = $assignment->build();
            $params = array_merge($params, $built_assignment->getParameters());
            return 'SET ' . $built_assignment->getString();
        }, $this->assignments);

        $query_string .= ' ' . implode(', ', $assignments_built);

        if ($this->condition_collection !== null) {
            $built_condition_collection = $this->condition_collection->build();
            $query_string .= ' WHERE ' . $built_condition_collection->getQueryString();
            $params = array_merge($params, $built_condition_collection->getParameters());
        }

        if (!empty($this->order_by)) {
            $query_string .= ' ORDER BY';

            $order_strings = array();

            foreach ($this->order_by as $order) {
                $order_string = ' `' . $order->getColumnName() . '`';

                switch($order->getDirection()) {
                    case ORDER_ASC:
                        $order_string .= ' ASC';
                        break;
                    case ORDER_DESC:
                        $order_string .= ' DESC';
                        break;
                }

                $order_strings[] = $order_string;
            }

            $query_string .= implode(', ', $order_strings);
        }

        if ($this->limit !== null) {
            $query_string .= ' LIMIT ' . $this->limit;
        }

        return new BuiltQuery($query_string, $params);
    }

    /**
     * @param ColumnStatement $column
     * @param $statement
     * @return $this
     */
    public function addAssignment($column, $statement) {
        return $this;
    }

    /**
     * @return Assignment[]
     */
    public function getAssignments() {
        return $this->assignments;
    }
}