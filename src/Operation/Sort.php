<?php

namespace Fasano\FluentStream\Operation;

use Closure;
use Fasano\FluentStream\Operation;

final readonly class Sort implements Operation
{
    public Closure $comparison;

    public function __construct(callable $comparison)
    {
        $this->comparison = Closure::fromCallable($comparison);
    }

    public function apply(iterable $input): iterable
    {
        $items = [];

        foreach ($input as $value) {
            $items[] = $value;
        }

        usort($items, fn($a, $b): mixed => ($this->comparison)($a, $b));

        foreach ($items as $item) {
            yield $item;
        }
    }
}