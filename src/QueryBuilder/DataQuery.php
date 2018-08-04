<?php


namespace QueryBuilder;


abstract class DataQuery extends Query
{
    /** @var Assignment[] */
    protected $assignments = [];

    /**
     * @param ColumnStatement $column
     * @param $statement
     * @return $this
     */
    public function addAssignment(ColumnStatement $column, Statement $statement) {
        $this->assignments[] = new Assignment($column, $statement);

        return $this;
    }

    /**
     * @return Assignment[]
     */
    public function getAssignments() {
        return $this->assignments;
    }
}