<?php declare(strict_types=1);

namespace QueryBuilderTests;

use Exception;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use QueryBuilder\UpdateQuery;

class UpdateQueryTest extends TestCase
{
    /**
     * Tests sanitising channel names from API.
     */
    public function testConstructWithNoValuesThrowsException(): void
    {
        try {
            new UpdateQuery('test', []);
        } catch (Exception $e) {
            $this->assertEquals(new Exception('You cannot create an update statement without fields and values to update'), $e);
        }
    }

    /**
     * Tests sanitising channel names from API.
     *
     * @param UpdateQuery $query Query
     * @param string $expected_sql expected_sql value
     * @param array $expected_data expected_sql bindable data
     */
    #[DataProvider('sqlProvider')]
    public function testSql(UpdateQuery $query, string $expected_sql, array $expected_data): void
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
        $update_single = [
            'query' => (new UpdateQuery('test', ['name' => 'dan'])),
            'expected_sql' => 'UPDATE test SET name = ?',
            'expected_data' => ['dan'],
        ];

        $update_multiple = [
            'query' => (new UpdateQuery('test', ['name' => 'dan', 'surname' => 'rogers'])),
            'expected_sql' => 'UPDATE test SET name = ?, surname = ?',
            'expected_data' => ['dan', 'rogers'],
        ];

        $one_condition = [
            'query' => (new UpdateQuery('test', ['name' => 'dan']))->where(['id' => 1]),
            'expected_sql' => 'UPDATE test SET name = ? WHERE id = ?',
            'expected_data' => ['dan', 1],
        ];

        $complex = [
            'query' => (new UpdateQuery('test', ['name' => 'dan', 'surname' => 'rogers']))
                ->where([
                    'id' => [1, 2, 3, 4, 5],
                    'name LIKE' => '%ogers'
                ])
                ->limit(1),
            'expected_sql' => 'UPDATE test SET name = ?, surname = ? WHERE id IN (?, ?, ?, ?, ?) AND name LIKE ? LIMIT 1',
            'expected_data' => ['dan', 'rogers', 1, 2, 3, 4, 5, '%ogers'],
        ];

        return compact(
            'update_single',
            'update_multiple',
            'one_condition',
            'complex',
        );
    }
}
