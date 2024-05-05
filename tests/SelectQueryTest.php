<?php declare(strict_types=1);

namespace QueryBuilderTests;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use QueryBuilder\Enum\Direction;
use QueryBuilder\SelectQuery;

class SelectQueryTest extends TestCase
{
    /**
     * Tests sanitising channel names from API.
     *
     * @param SelectQuery $query Query
     * @param string $expected_sql expected_sql value
     * @param array $expected_data expected_sql bindable data
     */
    #[DataProvider('sqlProvider')]
    public function testSql(SelectQuery $query, string $expected_sql, array $expected_data): void
    {
        $this->assertEquals($expected_sql, $query->sql());
        $this->assertEquals($expected_data, $query->params());
    }

    /**
     * Provider for test data for testSanitiseChannelName()
     *
     * @return array
     */
    public static function sqlProvider(): array
    {
        $simple = [
            'query' => new SelectQuery('test'),
            'expected_sql' => 'SELECT * FROM test',
            'expected_data' => [],
        ];

        $fields_only = [
            'query' => (new SelectQuery('test'))->fields(['id', 'name']),
            'expected_sql' => 'SELECT id, name FROM test',
            'expected_data' => [],
        ];

        $one_condition = [
            'query' => (new SelectQuery('test'))->where(['id' => 1]),
            'expected_sql' => 'SELECT * FROM test WHERE id = ?',
            'expected_data' => [1],
        ];

        $multiple_conditions = [
            'query' => (new SelectQuery('test'))->where(['id' => 1, 'name' => 'dan']),
            'expected_sql' => 'SELECT * FROM test WHERE id = ? AND name = ?',
            'expected_data' => [1, 'dan'],
        ];

        $in_condition = [
            'query' => (new SelectQuery('test'))->where(['name' => ['dan', 'rogers']]),
            'expected_sql' => 'SELECT * FROM test WHERE name IN (?, ?)',
            'expected_data' => ['dan', 'rogers'],
        ];

        $mixed_conditions = [
            'query' => (new SelectQuery('test'))->where(['id' => 1, 'name' => ['dan', 'rogers']]),
            'expected_sql' => 'SELECT * FROM test WHERE id = ? AND name IN (?, ?)',
            'expected_data' => [1, 'dan', 'rogers'],
        ];

        $limit = [
            'query' => (new SelectQuery('test'))->limit(1),
            'expected_sql' => 'SELECT * FROM test LIMIT 1',
            'expected_data' => [],
        ];

        $offset = [
            'query' => (new SelectQuery('test'))->offset(25),
            'expected_sql' => 'SELECT * FROM test OFFSET 25',
            'expected_data' => [],
        ];

        $group_single = [
            'query' => (new SelectQuery('test'))->groupBy('name'),
            'expected_sql' => 'SELECT * FROM test GROUP BY ?',
            'expected_data' => ['name'],
        ];

        $group_multiple = [
            'query' => (new SelectQuery('test'))->groupBy(['firstname', 'surname']),
            'expected_sql' => 'SELECT * FROM test GROUP BY ?, ?',
            'expected_data' => ['firstname', 'surname'],
        ];

        $order_single = [
            'query' => (new SelectQuery('test'))->orderBy('name', Direction::Descending),
            'expected_sql' => 'SELECT * FROM test ORDER BY ? DESC',
            'expected_data' => ['name'],
        ];

        $order_multiple = [
            'query' => (new SelectQuery('test'))->orderBy(['name', 'surname'], Direction::Ascending),
            'expected_sql' => 'SELECT * FROM test ORDER BY ?, ? ASC',
            'expected_data' => ['name', 'surname'],
        ];

        $complex = [
            'query' => (new SelectQuery('test'))
                ->fields(['id', 'name'])
                ->where([
                    'id' => [1, 2, 3, 4, 5],
                    'name LIKE' => '%og%',
                ])
                ->limit(25)
                ->offset(25)
                ->groupBy('name'),
            'expected_sql' => 'SELECT id, name FROM test WHERE id IN (?, ?, ?, ?, ?) AND name LIKE ? GROUP BY ? LIMIT 25 OFFSET 25',
            'expected_data' => [1, 2, 3, 4, 5, '%og%', 'name'],
        ];

        return compact(
            'simple',
            'fields_only',
            'one_condition',
            'multiple_conditions',
            'in_condition',
            'mixed_conditions',
            'limit',
            'offset',
            'group_single',
            'group_multiple',
            'complex',
            'order_single',
            'order_multiple',
        );
    }
}
