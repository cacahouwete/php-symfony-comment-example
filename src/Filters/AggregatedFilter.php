<?php

declare(strict_types=1);

namespace App\Filters;

use App\Filters\Items\FilterInterface;

final readonly class AggregatedFilter
{
    /**
     * @param array<string, FilterInterface> $filters
     */
    public function __construct(private readonly array $filters)
    {
    }

    /**
     * @param array<string, array<mixed>|bool|float|int|string> $values
     *
     * @return iterable<FilterWithValue>
     */
    public function apply(array $values): iterable
    {
        // TODO: Create unit test
        foreach ($values as $key => $value) {
            if (\is_array($value)) {
                /** @var bool|float|int|string $subValue */
                foreach ($value as $subKey => $subValue) {
                    $filterKey = sprintf('%s[%s]', $key, $subKey);
                    if (!\array_key_exists($filterKey, $this->filters)) {
                        continue;
                    }

                    yield new FilterWithValue($this->filters[$filterKey], $subValue);
                }
            } else {
                if (!\array_key_exists($key, $this->filters)) {
                    continue;
                }

                yield new FilterWithValue($this->filters[$key], $value);
            }
        }
    }
}
