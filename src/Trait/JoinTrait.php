<?php

declare(strict_types=1);

namespace QueryBuilder\Trait;

use QueryBuilder\Enum\Join;

trait JoinTrait
{
    /**
     * Joins
     *
     * @var array
     */
    protected array $joins = [];

    /**
     * Set joins
     *
     * @param Join $join_type Type of join to make
     * @param string $table Table to join
     * @param array $conditions Join conditions
     * @param bool $overrride Whether to merge or replace the ordering fields
     * @return self
     */
    public function join(Join $join_type, string $table, array $conditions, $override = false): self
    {
        return $this->assign('joins', [[$join_type, $table, $conditions]], $override);
    }

    /**
     * Create multiple JOIN statements
     *
     * @return void
     */
    protected function joinSql()
    {
        $join_string = '';
        foreach ($this->joins as $join) {
            $join_string .= $this->createJoinString(...$join) . ", ";
        }

        return trim($join_string, ', ');
    }

    /**
     * Create a JOIN statement
     *
     * @param Join $join_type
     * @param string $table
     * @param array $conditions
     * @return string
     */
    private function createJoinString(Join $join_type, string $table, array $conditions): string
    {
        $condition_string = '';
        foreach ($conditions as $key => $value) {
            $condition_string .= "$key = $value AND";
        }

        $condition_string = substr($condition_string, 0, -4);
        return "$join_type->value JOIN $table ON ($condition_string)";
    }
}
