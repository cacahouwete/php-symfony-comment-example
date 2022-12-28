<?php

declare(strict_types=1);

namespace App\Filters\Items;

use Doctrine\ORM\QueryBuilder;

final class PageFilter implements FilterInterface
{
    private ?int $page = null;

    public function __construct(readonly int $defaultItemPerPage)
    {
    }

    public function apply(QueryBuilder $queryBuilder, string $rootAlias, array|bool|float|int|string $value): void
    {
        if (!is_numeric($value)) {
            return;
        }

        $this->page = (int) $value;
        if ($this->page <= 0) {
            $this->page = 1;
        }

        $this->applyPageOnQueryBuilder($queryBuilder);
    }

    public function applyPageOnQueryBuilder(QueryBuilder $queryBuilder): void
    {
        if (null === $this->page) {
            return;
        }

        $itemsPerPage = $queryBuilder->getMaxResults();
        if (null === $itemsPerPage) {
            $queryBuilder->setMaxResults($this->defaultItemPerPage);
            $itemsPerPage = $this->defaultItemPerPage;
        }

        $queryBuilder->setFirstResult(($this->page - 1) * $itemsPerPage);
    }
}
