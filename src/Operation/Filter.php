<?php

namespace Fasano\FluentStream\Operation;

use Closure;
use Fasano\FluentStream\Operation;

final readonly class Filter implements Operation
{
    public Closure $predicate;

    public function __construct(callable $predicate)
    {
        $this->predicate = Closure::fromCallable($predicate);
    }

    public function apply(iterable $input): iterable
    {
        foreach ($input as $key => $value) {
            if (($this->predicate)($value, $key)) {
                yield $value;
            }
        }
    }
}