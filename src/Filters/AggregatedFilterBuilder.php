<?php

declare(strict_types=1);

namespace App\Filters;

use App\Filters\Items\FilterInterface;

final class AggregatedFilterBuilder
{
    /**
     * @var array<string,FilterInterface>
     */
    private array $filters = [];

    public function create(): self
    {
        $this->filters = [];

        return $this;
    }

    public function addFilter(FilterInterface $filter, string $key): self
    {
        $this->filters[$key] = $filter;

        return $this;
    }

    public function build(): AggregatedFilter
    {
        return new AggregatedFilter($this->filters);
    }
}
