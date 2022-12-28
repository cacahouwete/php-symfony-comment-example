<?php

declare(strict_types=1);

namespace App\Usecases;

use App\Entity\Comment;
use App\Filters\AggregatedFilter;
use App\Filters\AggregatedFilterBuilder;
use App\Filters\Items\ExistFilter;
use App\Filters\Items\ItemsPerPageFilter;
use App\Filters\Items\OrderFilter;
use App\Filters\Items\PageFilter;
use App\Filters\Items\SearchFilter;
use App\Filters\ParamNameGenerator;
use App\Gateway\CommentGatewayInterface;

final readonly class CommentGetCollectionByFiltersUsecase
{
    private AggregatedFilter $commentFilter;

    public function __construct(private readonly CommentGatewayInterface $commentGateway)
    {
        $builder = new AggregatedFilterBuilder();

        $pageFilter = new PageFilter(30);

        $this->commentFilter = $builder->create()
            ->addFilter($pageFilter, 'page')
            ->addFilter(new ItemsPerPageFilter($pageFilter, 50), 'itemsPerPage')
            ->addFilter(new SearchFilter(new ParamNameGenerator(), 'groupKey'), 'groupKey')
            ->addFilter(new OrderFilter('createdAt'), 'order[createdAt]')
            ->addFilter(new ExistFilter('parent'), 'exists[parent]')
            ->build()
        ;
    }

    /**
     * @param array<string, array<mixed>|bool|float|int|string> $filters
     *
     * @return iterable<Comment>
     */
    public function __invoke(array $filters): iterable
    {
        return $this->commentGateway->findByFilter(
            $this->commentFilter->apply(array_merge(['page' => 1], $filters))
        );
    }
}
