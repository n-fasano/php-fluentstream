<?php

namespace Fasano\FluentStream\Operation;

use Fasano\FluentStream\Operation;

final readonly class Chunk implements Operation
{
    public function __construct(
        public int $size,
    ) {}

    public function apply(iterable $input): iterable
    {
        $chunk = [];
        $i = 0;

        foreach ($input as $value) {
            if ($this->size <= $i++) {
                yield $chunk;
                $chunk = [];
            }

            $chunk[] = $value;
        }

        if (!empty($chunk)) {
            yield $chunk;
        }
    }
}