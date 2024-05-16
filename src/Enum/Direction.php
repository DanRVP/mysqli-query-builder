<?php

declare(strict_types=1);

namespace QueryBuilder\Enum;

enum Direction: string
{
    case Ascending = 'ASC';
    case Descending = 'DESC';
}
