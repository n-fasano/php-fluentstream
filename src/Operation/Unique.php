<?php

namespace Fasano\FluentStream\Operation;

use Fasano\FluentStream\Operation;

final class Unique implements Operation
{
    private array $seen = [];
    
    public function apply(iterable $input): iterable
    {
        foreach ($input as $value) {
            $key = is_object($value) ? spl_object_hash($value) : serialize($value);

            if (!isset($this->seen[$key])) {
                $this->seen[$key] = true;
                yield $value;
            }
        }
    }
}