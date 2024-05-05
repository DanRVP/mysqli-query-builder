<?php declare(strict_types=1);

namespace QueryBuilderTests;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use QueryBuilder\Enum\Direction;
use QueryBuilder\SelectQuery;
use Reflection;

class SelectQueryTest extends TestCase
{
/**
     * Tests sanitising channel names from API.
     *
     * @param string $name Channel name to evaluate
     * @param string $expected Expected value
     */
    #[DataProvider('sqlProvider')]
    public function testSql(SelectQuery $query, string $expected): void
    {
        $this->assertEquals($expected, $query->sql());
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
            'expected' => 'SELECT * FROM test',
        ];

        $fields_only = [
            'query' => (new SelectQuery('test'))->fields(['id', 'name']),
            'expected' => 'SELECT id, name FROM test',
        ];

        $one_condition = [
            'query' => (new SelectQuery('test'))->where(['id' => 1]),
            'expected' => 'SELECT * FROM test WHERE id = ?',
        ];

        $multiple_conditions = [
            'query' => (new SelectQuery('test'))->where(['id' => 1, 'name' => 'dan']),
            'expected' => 'SELECT * FROM test WHERE id = ? AND name = ?',
        ];

        $in_condition = [
            'query' => (new SelectQuery('test'))->where(['name' => ['dan', 'rogers']]),
            'expected' => 'SELECT * FROM test WHERE name IN (?, ?)',
        ];

        $mixed_conditions = [
            'query' => (new SelectQuery('test'))->where(['id' => 1, 'name' => ['dan', 'rogers']]),
            'expected' => 'SELECT * FROM test WHERE id = ? AND name IN (?, ?)',
        ];

        $limit = [
            'query' => (new SelectQuery('test'))->limit(1),
            'expected' => 'SELECT * FROM test LIMIT 1',
        ];

        $offset = [
            'query' => (new SelectQuery('test'))->offset(25),
            'expected' => 'SELECT * FROM test OFFSET 25',
        ];

        $group_single = [
            'query' => (new SelectQuery('test'))->groupBy('name'),
            'expected' => 'SELECT * FROM test GROUP BY ?',
        ];

        $group_multiple = [
            'query' => (new SelectQuery('test'))->groupBy(['firstname', 'surname']),
            'expected' => 'SELECT * FROM test GROUP BY ?, ?',
        ];

        $order_single = [
            'query' => (new SelectQuery('test'))->orderBy('name', Direction::Descending),
            'expected' => 'SELECT * FROM test ORDER BY ? DESC',
        ];

        $complex = [
            'query' => (new SelectQuery('test'))
                ->fields(['id', 'name'])
                ->where([
                    'id' => [1, 2, 3, 4, 5],
                    'name LIKE' => '%og%'
                ])
                ->limit(25)
                ->offset(25)
                ->groupBy('name'),
            'expected' => 'SELECT id, name FROM test WHERE id IN (?, ?, ?, ?, ?) AND name LIKE ? GROUP BY ? LIMIT 25 OFFSET 25',
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
        );
    }
}
