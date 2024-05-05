<?php declare(strict_types=1);

namespace QueryBuilderTests;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use QueryBuilder\DeleteQuery;

class DeleteQueryTest extends TestCase
{
/**
     * Tests sanitising channel names from API.
     *
     * @param DeleteQuery $query Query
     * @param string $expected_sql expected_sql value
     * @param array $expected_data expected_sql bindable data
     */
    #[DataProvider('sqlProvider')]
    public function testSql(DeleteQuery $query, string $expected_sql, array $expected_data): void
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
            'query' => new DeleteQuery('test'),
            'expected_sql' => 'DELETE FROM test',
            'expected_data' => [],
        ];

        $one_condition = [
            'query' => (new DeleteQuery('test'))->where(['id' => 1]),
            'expected_sql' => 'DELETE FROM test WHERE id = ?',
            'expected_data' => [1],
        ];

        $multiple_conditions = [
            'query' => (new DeleteQuery('test'))->where(['id' => 1, 'name' => 'dan']),
            'expected_sql' => 'DELETE FROM test WHERE id = ? AND name = ?',
            'expected_data' => [1, 'dan'],
        ];

        $in_condition = [
            'query' => (new DeleteQuery('test'))->where(['name' => ['dan', 'rogers']]),
            'expected_sql' => 'DELETE FROM test WHERE name IN (?, ?)',
            'expected_data' => ['dan', 'rogers'],
        ];

        $mixed_conditions = [
            'query' => (new DeleteQuery('test'))->where(['id' => 1, 'name' => ['dan', 'rogers']]),
            'expected_sql' => 'DELETE FROM test WHERE id = ? AND name IN (?, ?)',
            'expected_data' => [1, 'dan', 'rogers'],
        ];

        return compact(
            'simple',
            'one_condition',
            'multiple_conditions',
            'in_condition',
            'mixed_conditions',
        );
    }
}
