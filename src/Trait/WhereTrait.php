<?php

declare(strict_types=1);

namespace QueryBuilder\Trait;

trait WhereTrait
{
    /**
     * Where conditions
     *
     * @var array
     */
    protected array $conditions = [];

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
     * Create a WHERE condition string
     *
     * @param array $conditions
     * @return string
     */
    protected function whereSql(array $conditions): string
    {
        $where = '';
        foreach ($conditions as $key => $value) {
            if (substr(strtoupper((string) $key), 0, 2) === 'OR' && is_array($value)) {
                $or = '';
                foreach ($value as $sub_key => $sub_value) {
                    $or .= $this->createWhereConditionString($sub_key, $sub_value) . ' OR ';
                }

                $where .= '(' . substr($or, 0, -4) . ') AND ';
            } else {
                $where .= $this->createWhereConditionString($key, $value) . ' AND ';
            }
        }

        return 'WHERE ' . substr($where, 0, -5);
    }

    /**
     * Create a subset of a where condition (=, LIKE, IN)
     *
     * @param string $key
     * @param mixed $value
     * @return string
     */
    private function createWhereConditionString(string $key, mixed $value): string
    {
        if (is_array($value)) {
            return "$key IN (" . implode(', ', array_pad([], count($value), '?')) . ")";
        } elseif (count($parts = explode(' ', $key, 2)) === 2) {
            return "$parts[0] $parts[1] ?";
        } else {
            return "$key = ?";
        }
    }
}
