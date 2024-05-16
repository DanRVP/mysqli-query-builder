<?php

declare(strict_types=1);

namespace QueryBuilder\Trait;

trait LimitTrait
{
    /**
     * Query limit
     *
     * @var int|null
     */
    protected ?int $limit = null;

    /**
     * Limit the number of records returned
     *
     * @param int $limit The limit to assign
     * @return self
     */
    public function limit(int $limit): self
    {
        $this->limit = $limit;
        return $this;
    }

    /**
     * Create a LIMT condition
     *
     * @return string
     */
    protected function limitSql(): string
    {
        return "LIMIT $this->limit";
    }
}
