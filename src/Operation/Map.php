<?php

namespace Fasano\FluentStream\Operation;

use Closure;
use Fasano\FluentStream\Operation;

final readonly class Map implements Operation
{
    public Closure $mutation;

    public function __construct(callable $mutation)
    {
        $this->mutation = Closure::fromCallable($mutation);
    }

    public function apply(iterable $input): iterable
    {
        foreach ($input as $key => $value) {
            yield ($this->mutation)($value, $key);
        }
    }
}