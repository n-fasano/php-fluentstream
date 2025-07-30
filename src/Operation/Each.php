<?php

namespace Fasano\FluentStream\Operation;

use Closure;
use Fasano\FluentStream\Operation;

final readonly class Each implements Operation
{
    public Closure $sideEffect;

    public function __construct(callable $sideEffect)
    {
        $this->sideEffect = Closure::fromCallable($sideEffect);
    }

    public function apply(iterable $input): iterable
    {
        foreach ($input as $key => $value) {
            ($this->sideEffect)($value, $key);

            yield $value;
        }
    }
}