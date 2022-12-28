<?php

declare(strict_types=1);

namespace App\Filters\Items;

use Doctrine\ORM\QueryBuilder;

final readonly class ExistFilter implements FilterInterface
{
    public function __construct(private readonly string $field)
    {
    }

    public function apply(QueryBuilder $queryBuilder, string $rootAlias, array|bool|float|int|string $value): void
    {
        $queryBuilder
            ->andWhere($rootAlias.'.'.$this->field.' '.('false' === $value || 0 === $value ? 'IS NULL' : 'IS NOT NULL'))
        ;
    }
}
