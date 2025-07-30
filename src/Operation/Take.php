<?php

namespace Fasano\FluentStream\Operation;

use Fasano\FluentStream\Operation;

final readonly class Take implements Operation
{
    public function __construct(
        public int $amount,
    ) {}

    public function apply(iterable $input): iterable
    {
        $count = 0;

        foreach ($input as $value) {
            if ($this->amount <= $count++) {
                break;
            }

            yield $value;
        }
    }
}