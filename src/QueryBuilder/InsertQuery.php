<?php

namespace QueryBuilder;


class InsertQuery extends DataQuery
{
    /** @var int */
    private $priority = QueryPriority::NONE;

    /** @var bool */
    private $ignore = false;

    /**
     * @param string $table_name
     */
    public function __construct(string $table_name)
    {
        parent::__construct($table_name);
    }

    /**
     * @param int $priority
     * @return $this
     */
    public function setPriority(int $priority)
    {
        $this->priority = $priority;

        return $this;
    }

    /**
     * @param bool $ignore
     * @return $this
     */
    public function setIgnore(bool $ignore)
    {
        $this->ignore = $ignore;

        return $this;
    }

    /**
     * @return BuiltQuery
     * @throws InvalidQueryException
     */
    public function build()
    {
        if (count($this->assignments) === 0)
            throw new InvalidQueryException('You must supply at least one assignment for an INSERT query');

        $builder = new QueryStringBuilder('INSERT');

        switch ($this->priority) {
            case QueryPriority::LOW:
                $builder->append(' LOW_PRIORITY');
                break;
            case QueryPriority::HIGH:
                $builder->append(' HIGH_PRIORITY');
                break;
        }

        if ($this->ignore)
            $builder->append(' IGNORE');

        $builder->append(' INTO `%s`', $this->table_name);

        // TODO: PARTITION

        if (!empty($this->assignments)) {
            $builder->append(' SET ');
            $builder->appendBuildableCollection($this->assignments);
        }

        // TODO: ON DUPLICATE KEY UPDATE

        return $builder->toBuiltQuery();
    }
}