<?php

declare(strict_types=1);

namespace App\Filters\Items;

use App\Filters\ParamNameGenerator;
use Doctrine\ORM\QueryBuilder;

final readonly class SearchFilter implements FilterInterface
{
    public function __construct(private readonly ParamNameGenerator $paramNameGenerator, private readonly string $field)
    {
    }

    public function apply(QueryBuilder $queryBuilder, string $rootAlias, array|bool|float|int|string $value): void
    {
        $paramName = $this->paramNameGenerator->generate($this->field);
        $queryBuilder
            ->andWhere($rootAlias.'.'.$this->field.' = :'.$paramName)
            ->setParameter($paramName, $value)
        ;
    }
}
