<?php

declare(strict_types=1);

namespace QueryBuilder;

use QueryBuilder\Trait\JoinTrait;
use QueryBuilder\Trait\LimitTrait;
use QueryBuilder\Trait\OrderByTrait;
use QueryBuilder\Trait\WhereTrait;

class SelectQuery extends AbstractQuery
{
    use JoinTrait;
    use LimitTrait;
    use OrderByTrait;
    use WhereTrait;

    /**
     * Select fields
     *
     * @var array
     */
    protected array $fields = [];

    /**
     * Query offset
     *
     * @var int|null
     */
    protected ?int $offset = null;

    /**
     * Fields to group by
     *
     * @var array
     */
    protected array $group_by = [];

    /**
     * @inheritDoc
     */
    public function sql(): string
    {
        $select = '*';
        if (!empty($this->fields)) {
            $select = implode(', ', $this->fields);
        }

        $query_string = "SELECT $select FROM $this->table";
        if (!empty($this->joins)) {
            $query_string .= ' ' . $this->joinSql($this->joins);
        }

        if (!empty($this->conditions)) {
            $query_string .= ' ' . $this->whereSql($this->conditions);
        }

        if (!empty($this->group_by)) {
            $query_string .= ' GROUP BY ' . implode(', ', $this->group_by);
        }

        if (!empty($this->order_by)) {
            $query_string .= ' ' . $this->orderBySql();
        }

        if (!empty($this->limit)) {
            $query_string .= ' ' . $this->limitSql($this->limit);
        }

        if (!is_null($this->offset)) {
            $query_string .= ' OFFSET ' . $this->offset;
        }

        return $query_string;
    }

    /**
     * @inheritDoc
     */
    public function params(): array
    {
        $conditions = [];
        foreach ($this->conditions as $condition) {
            if (is_array($condition)) {
                $conditions = array_merge($conditions, array_values($condition));
            } else {
                $conditions[] = $condition;
            }
        }

        return array_merge($conditions);
    }

    /**
     * Set the fields to be selected
     *
    * @param array $fields List of fields to select
     * @param bool $override Set to true to assign the fields in stead of merging them
     * @return self
     */
    public function fields(array $fields, bool $override = false): self
    {
        return $this->assign('fields', $fields, $override);
    }

    /**
     * Set the offset for the query
     *
     * @param int $offset The offset to assign
     * @return self
     */
    public function offset(int $offset): self
    {
        $this->offset = $offset;
        return $this;
    }

    /**
     * Set the fields to group by
     *
     * @param array|string $group_by Single or multiple fields to group
     * @param bool $override Set to true to assign the fields in stead of merging them
     * @return self
     */
    public function groupBy(array|string $group_by, bool $override = false): self
    {
        if (is_string($group_by)) {
            $group_by = [$group_by];
        }

        return $this->assign('group_by', $group_by, $override);
    }
}
