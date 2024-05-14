<?php declare(strict_types=1);

namespace QueryBuilder;

use QueryBuilder\Condition\ConditionFactory;
use QueryBuilder\Enum\Direction;

class SelectQuery extends AbstractQuery
{
    /**
     * Select fields
     *
     * @var array
     */
    protected array $fields = [];

    /**
     * Where conditions
     *
     * @var array
     */
    protected array $conditions = [];

    /**
     * Query limit
     *
     * @var int|null
     */
    protected ?int $limit = null;

    /**
     * Query offset
     *
     * @var int|null
     */
    protected ?int $offset = null;

    /**
     * Order direction `ASC` or `DESC`
     *
     * @var string
     */
    protected string $order_direction;

    /**
     * Fields to order by
     *
     * @var array
     */
    protected array $order_by = [];

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
        if (!empty($this->conditions)) {
            $query_string .= ' ' . ConditionFactory::createWhere($this->conditions);
        }

        if (!empty($this->group_by)) {
            $query_string .= ' GROUP BY ' . implode(', ', $this->group_by);
        }

        if (!empty($this->order_by)) {
            $query_string .= ' ORDER BY ' . implode(', ', $this->order_by) . ' ' . $this->order_direction;
        }

        if (!empty($this->limit)) {
            $query_string .= ' ' . ConditionFactory::createLimit($this->limit);
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
     * Set the where conditions
     *
     * @param array $conditions List of conditions to filter the query by
     * @param bool $override Set to true to assign the conditions in stead of merging them
     * @return self
     */
    public function where(array $conditions, bool $override = false): self
    {
        return $this->assign('conditions', $conditions, $override);
    }

    /**
     * Limit the number of records returned
     *
     * @param int $limit The limit to assign
     * @return self
     */
    public function limit(int $limit): self
    {
        $this->limit = $limit;
        return $this;
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

    /**
     * Set ordering.
     *
     * @param array|string $order_by Field or fields to order by
     * @param Direction $direction ASC or DESC Will always be overidden
     * @param bool $overrride Whether to merge or replace the ordering fields
     * @return self
     */
    public function orderBy(array|string $order_by, Direction $direction, bool $override = false): self
    {
        if (is_string($order_by)) {
            $order_by = [$order_by];
        }

        $this->order_direction = $direction->value;
        return $this->assign('order_by', $order_by, $override);
    }
}
