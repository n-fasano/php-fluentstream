<?php

namespace Fasano\FluentStream;

interface Operation
{
    /**
     * Apply this operation to the iterator
     * 
     * @param iterable $input The input iterator
     * @return iterable The transformed iterator
     */
    public function apply(iterable $input): iterable;
}