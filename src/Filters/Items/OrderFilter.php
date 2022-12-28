<?php

declare(strict_types=1);

namespace App\Filters\Items;

use Doctrine\ORM\QueryBuilder;

final readonly class OrderFilter implements FilterInterface
{
    public const VALUES = ['asc', 'desc'];

    public function __construct(private readonly string $field)
    {
    }

    public function apply(QueryBuilder $queryBuilder, string $rootAlias, array|bool|float|int|string $value): void
    {
        if (!\in_array($value, self::VALUES, true)) {
            return;
        }

        $queryBuilder
            ->addOrderBy($rootAlias.'.'.$this->field, $value)
        ;
    }
}
