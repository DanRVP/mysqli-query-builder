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

    /**
     * Assign or merge values to a property
     *
     * @param string $property Name of the property to assign to
     * @param array $values Values to assign
     * @param bool $override Whether to assingn the values or replace the values
     * @return self
     */
    protected function assign(string $property, array $values, bool $override): self
    {
        if ($override) {
            $this->{$property} = $values;
        } else {
            $this->{$property} = array_merge($this->{$property}, $values);
        }

        return $this;
    }
}
