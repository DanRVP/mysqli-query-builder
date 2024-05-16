<?php

declare(strict_types=1);

namespace QueryBuilder\Trait;

use QueryBuilder\Enum\Direction;

trait OrderByTrait
{
    /**
     * Order direction `ASC` or `DESC`
     *
     * @var string
     */
    protected string $order_direction;

    /**
     * Fields to order by
     *
     * @var array
     */
    protected array $order_by = [];

    /**
     * Set ordering.
     *
     * @param array|string $order_by Field or fields to order by
     * @param Direction $direction ASC or DESC Will always be overidden
     * @param bool $overrride Whether to merge or replace the ordering fields
     * @return self
     */
    public function orderBy(array|string $order_by, Direction $direction, bool $override = false): self
    {
        if (is_string($order_by)) {
            $order_by = [$order_by];
        }

        $this->order_direction = $direction->value;
        return $this->assign('order_by', $order_by, $override);
    }

    /**
     * Create a LIMT condition
     *
     * @param string $limit
     * @return string
     */
    protected function orderBySql(): string
    {
        return 'ORDER BY ' . implode(', ', $this->order_by) . ' ' . $this->order_direction;
    }
}
