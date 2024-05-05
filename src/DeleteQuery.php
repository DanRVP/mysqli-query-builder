<?php declare(strict_types=1);

namespace QueryBuilder;

use QueryBuilder\Condition\ConditionFactory;

class DeleteQuery extends AbstractQuery
{
    /**
     * Where conditions
     *
     * @var array
     */
    protected array $conditions = [];

    /**
     * @inheritDoc
     */
    public function sql(): string
    {
        $query_string = "DELETE FROM $this->table";
        if (!empty($this->conditions)) {
            $query_string .= ' ' . ConditionFactory::createWhere($this->conditions);
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
}
