<?php declare(strict_types=1);

namespace QueryBuilder;

use Exception;
use QueryBuilder\Condition\ConditionFactory;

class UpdateQuery extends AbstractQuery
{
    /**
     * Update values
     *
     * @var array
     */
    protected array $values = [];

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
     * @inheritDoc
     * @throws Exception
     */
    public function sql(): string
    {
        if (empty($fields) || empty($values)) {
            throw new Exception('You cannot create an update statement without fields and values to update');
        }

        $query_string = "UPDATE $this->table ";
        if (!empty($this->conditions)) {
            $query_string .= ' ' . ConditionFactory::createWhere($this->conditions);
        }

        if (!empty($this->limit)) {
            $query_string .= ' ' . ConditionFactory::createLimit($this->limit);
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

        return $conditions;
    }

    /**
     * Set the fields and values to be updated
     *
     * @param array $values Associative array of keys and values to update
     * @param bool $override Set to true to assign the values instead of merging them
     * @return self
     */
    public function values(array $values, bool $override = false): self
    {
        if ($override) {
            $this->values = $values;
        } else {
            $this->values = array_merge($this->values, $values);
        }

        return $this;
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
        if ($override) {
            $this->conditions = $conditions;
        } else {
            $this->conditions = array_merge($this->conditions, $conditions);
        }

        return $this;
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
}
