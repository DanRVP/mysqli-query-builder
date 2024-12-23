<?php

declare(strict_types=1);

namespace QueryBuilder;

class RawQuery extends AbstractQuery
{
    /**
     * Raw SQL query
     *
     * @var string
     */
    protected string $sql;

    /**
     * SQL params to bind
     *
     * @var array
     */
    protected array $params;

    /**
     * @inheritDoc
     */
    public function sql(): string
    {
        return $this->sql;
    }

    /**
     * @inheritDoc
     */
    public function params(): array
    {
        return $this->params;
    }

    /**
     * Set the value of sql
     */
    public function setSql(string $sql): self
    {
        $this->sql = $sql;
        return $this;
    }

    /**
     * Set the value of params
     */
    public function setParams(array $params): self
    {
        $this->params = $params;
        return $this;
    }
}
