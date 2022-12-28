<?php

declare(strict_types=1);

namespace App\Filters\Items;

use Doctrine\ORM\QueryBuilder;

final readonly class ItemsPerPageFilter implements FilterInterface
{
    public function __construct(public PageFilter $pageFilter, public int $maxItemPerPage)
    {
    }

    public function apply(QueryBuilder $queryBuilder, string $rootAlias, array|bool|float|int|string $value): void
    {
        if (!is_numeric($value) || (int) $value <= 0) {
            return;
        }

        $itemsPerPage = (int) $value;
        if ($itemsPerPage > $this->maxItemPerPage) {
            $itemsPerPage = $this->maxItemPerPage;
        }

        $queryBuilder->setMaxResults($itemsPerPage);

        $this->pageFilter->applyPageOnQueryBuilder($queryBuilder);
    }
}
