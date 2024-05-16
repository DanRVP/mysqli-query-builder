<?php

declare(strict_types=1);

namespace QueryBuilder\Enum;

enum Join: string
{
    case Inner = 'INNER';
    case Left = 'LEFT';
    case Right = 'RIGHT';
    case Cross = 'CROSS';
}
