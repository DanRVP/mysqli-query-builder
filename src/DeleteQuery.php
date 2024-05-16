<?php

declare(strict_types=1);

namespace QueryBuilder;

use QueryBuilder\Trait\JoinTrait;
use QueryBuilder\Trait\LimitTrait;
use QueryBuilder\Trait\OrderByTrait;
use QueryBuilder\Trait\WhereTrait;

class DeleteQuery extends AbstractQuery
{
    use JoinTrait;
    use LimitTrait;
    use OrderByTrait;
    use WhereTrait;

    /**
     * @inheritDoc
     */
    public function sql(): string
    {
        $query_string = "DELETE FROM $this->table";
        if (!empty($this->joins)) {
            $query_string .= ' ' . $this->joinSql($this->joins);
        }

        if (!empty($this->conditions)) {
            $query_string .= ' ' . $this->whereSql($this->conditions);
        }

        if (!empty($this->order_by)) {
            $query_string .= ' ' . $this->orderBySql();
        }

        if (!empty($this->limit)) {
            $query_string .= ' ' . $this->limitSql($this->limit);
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
}
