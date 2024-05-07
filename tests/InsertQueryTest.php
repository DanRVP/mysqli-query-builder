<?php declare(strict_types=1);

namespace QueryBuilderTests;

use Exception;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use QueryBuilder\InsertQuery;

class InsertQueryTest extends TestCase
{
    /**
     * Tests sanitising channel names from API.
     */
    public function testConstructWithNoValuesThrowsException(): void
    {
        try {
            new InsertQuery('test', []);
        } catch (Exception $e) {
            $this->assertEquals(new Exception('You cannot create an insert statement without fields and values to insert'), $e);
        }
    }

    /**
     * Tests sanitising channel names from API.
     *
     * @param InsertQuery $query Query
     * @param string $expected_sql expected_sql value
     * @param array $expected_data expected_sql bindable data
     */
    #[DataProvider('sqlProvider')]
    public function testSql(InsertQuery $query, string $expected_sql, array $expected_data): void
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
        $insert_1_column = [
            'query' => (new InsertQuery('test', ['name' => 'dan'])),
            'expected_sql' => 'INSERT INTO test (name) VALUES (?)',
            'expected_data' => ['dan'],
        ];

        $insert_multiple_columns = [
            'query' => (new InsertQuery('test', ['name' => 'dan', 'surname' => 'rogers'])),
            'expected_sql' => 'INSERT INTO test (name, surname) VALUES (?, ?)',
            'expected_data' => ['dan', 'rogers'],
        ];

        return compact(
            'insert_1_column',
            'insert_multiple_columns',
        );
    }
}
