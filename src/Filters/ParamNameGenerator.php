<?php

declare(strict_types=1);

namespace App\Filters;

final class ParamNameGenerator
{
    private int $increment = 0;

    public function generate(string $name): string
    {
        $result = sprintf('%s_%d', $name, $this->increment);

        ++$this->increment;

        return $result;
    }
}
