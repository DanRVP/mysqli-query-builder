<?php declare(strict_types=1);

namespace QueryBuilder\Condition;

/**
 * Factory to create reusable query elements
 */
class ConditionFactory
{
    /**
     * Create a WHERE condition string
     *
     * @param array $conditions
     * @return string
     */
    public static function createWhere(array $conditions): string
    {
        $where = '';
        foreach ($conditions as $key => $value) {
            if (substr(strtoupper((string) $key), 0, 2) === 'OR' && is_array($value)) {
                $or = '';
                foreach ($value as $sub_key => $sub_value) {
                    $or .= self::createWhereConditionString($sub_key, $sub_value) . ' OR ';
                }

                $where .= '(' . substr($or, 0, -4) . ') AND ';
            } else {
                $where .= self::createWhereConditionString($key, $value) . ' AND ';
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
    private static function createWhereConditionString(string $key, mixed $value): string
    {
        if (is_array($value)) {
            return "$key IN (" . implode(', ', array_pad([], count($value), '?')) . ")";
        } elseif (str_contains($key, 'LIKE')) {
            return "$key ?";
        } else {
            return "$key = ?";
        }
    }

    /**
     * Create a LIMT condition
     *
     * @param string $limit
     * @return string
     */
    public static function createLimit(int $limit): string
    {
        return "LIMIT $limit";
    }
}