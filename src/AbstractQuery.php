<?php declare(strict_types=1);

namespace QueryBuilder;

/**
 * Interface which all queries must satisfy
 */
abstract class AbstractQuery
{
    /**
     * Convert this query to a mysqli paramaterised query and return the query
     *
     * @return string
     */
    abstract public function sql(): string;

    /**
     * returns a non-associative array of params ordered for the query
     *
     * @return array
     */
    abstract public function params(): array;

    /**
     * Constructor
     *
     * @param string $table
     */
    public function __construct(protected string $table) {}
}
