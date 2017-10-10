<?php

namespace QueryBuilder;


class DeleteQuery extends Query {
    /** @var bool */
    private $low_priority;

    /** @var bool */
    private $quick;

    /** @var bool */
    private $ignore;

    /** @var ConditionCollection */
    private $condition_collection;

    /** @var OrderByColumn[] */
    private $order_by = [];

    /** @var int */
    private $limit;

    /**
     * @return BuiltQuery
     */
    public function build() {
        $builder = new QueryStringBuilder('DELETE');

        if ($this->low_priority)
            $builder->append(' LOW_PRIORITY');

        if ($this->quick)
            $builder->append(' QUICK');

        if ($this->ignore)
            $builder->append(' IGNORE');

        $builder->append(' FROM ' . $this->table_name);

        if ($this->condition_collection !== null) {
            $builder->append(' WHERE ');
            $builder->appendStatement($this->condition_collection->build());
        }

        if (!empty($this->order)) {
            foreach ($this->order_by as $order) {
                $builder->append($order->());
            }
        }

        return $builder->toBuiltQuery();
    }
}