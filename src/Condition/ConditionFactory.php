<?php declare(strict_types=1);

namespace QueryBuilder\Condition;

/**
 * Factory to create reusable query elements
 */
class ConditionFactory
{
    public static function createWhere(array $conditions)
    {
        $where = 'WHERE ';
        foreach ($conditions as $key => $value) {
            if (is_array($value)) {
                $where .= "$key IN (" . implode(', ', array_pad([], count($value), '?')) . ")";
            } elseif (str_contains($key, 'LIKE')) {
                $where .= "$key ?";
            } else {
                $where .= "$key = ?";
            }

            $where .= ' AND ';
        }

        return substr($where, 0, -5);
    }

    public static function createLimit(int $limit)
    {
        return "LIMIT $limit";
    }
}