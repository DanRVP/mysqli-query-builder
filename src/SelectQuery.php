<?php declare(strict_types=1);

namespace QueryBuilder;

use QueryBuilder\Condition\ConditionFactory;

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
            $query_string .= ' GROUP BY ' . implode(', ', array_pad([], count($this->group_by), '?'));
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
        return array_merge(
            array_values($this->conditions),
            array_values($this->group_by)
        );
    }

    public function fields(array $fields, bool $override = false): self
    {
        if ($override) {
            $this->fields = $fields;
        } else {
            $this->fields = array_merge($this->fields, $fields);
        }

        return $this;
    }

    public function where(array $conditions, bool $override = false): self
    {
        if ($override) {
            $this->conditions = $conditions;
        } else {
            $this->conditions = array_merge($this->conditions, $conditions);
        }

        return $this;
    }

    public function limit(int $limit): self
    {
        $this->limit = $limit;
        return $this;
    }

    public function offset(int $offset): self
    {
        $this->offset = $offset;
        return $this;
    }

    public function groupBy(array|string $group_by, bool $override = false): self
    {
        if (is_string($group_by)) {
            $group_by = [$group_by];
        }

        if ($override) {
            $this->group_by = $group_by;
        } else {
            $this->group_by = array_merge($this->group_by, $group_by);
        }

        return $this;
    }
}
