<?php

declare(strict_types=1);

namespace App\Filters;

use App\Filters\Items\FilterInterface;
use Doctrine\ORM\QueryBuilder;

final readonly class FilterWithValue
{
    /**
     * @param array<mixed>|bool|float|int|string $value
     */
    public function __construct(private readonly FilterInterface $filter, private readonly array|bool|float|int|string $value)
    {
    }

    public function apply(QueryBuilder $queryBuilder, string $rootAlias): void
    {
        $this->filter->apply($queryBuilder, $rootAlias, $this->value);
    }
}
