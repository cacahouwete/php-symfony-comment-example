<?php

declare(strict_types=1);

namespace App\Filters\Items;

use Doctrine\ORM\QueryBuilder;

interface FilterInterface
{
    /**
     * @param array<mixed>|bool|float|int|string $value
     */
    public function apply(QueryBuilder $queryBuilder, string $rootAlias, array|bool|float|int|string $value): void;
}
