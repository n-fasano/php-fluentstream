<?php

namespace Fasano\FluentStream;

use Fasano\FluentStream\Operation\Chunk;
use Fasano\FluentStream\Operation\Each;
use Fasano\FluentStream\Operation\Filter;
use Fasano\FluentStream\Operation\Map;
use Fasano\FluentStream\Operation\Skip;
use Fasano\FluentStream\Operation\Sort;
use Fasano\FluentStream\Operation\Take;
use Fasano\FluentStream\Operation\Unique;

/**
 * This used to be a trait, but type inference for Workflows didn't work well. Example:
 *  
 * $first25Squared = Workflow::create() 
 *    ->map(fn (int $x): int => $x ** 2)
 *    ->take(25);
 * 
 * Would get inferred as: Workflow<U> $first25Squared
 * 
 * @template T
 */
abstract readonly class Operations
{
    abstract protected function addOperation(Operation $operation): static;

    /**
     * @template U
     * 
     * @param callable(T): U $mutation
     * 
     * @return static<U>
     */
    public function map(callable $mutation): static
    {
        return $this->addOperation(new Map($mutation));
    }

    /**
     * @param callable(T): T $sideEffect
     * 
     * @return static<T>
     */
    public function each(callable $sideEffect): static
    {
        return $this->addOperation(new Each($sideEffect));
    }

    /**
     * @return static<T>
     */
    public function take(int $amount): static
    {
        return $this->addOperation(new Take($amount));
    }

    /**
     * @param callable(T): bool $predicate
     * 
     * @return static<T>
     */
    public function filter(callable $predicate): static
    {
        return $this->addOperation(new Filter($predicate));
    }

    /**
     * @return static<T>
     */
    public function skip(int $amount): static
    {
        return $this->addOperation(new Skip($amount));
    }

    /**
     * @return static<T>
     */
    public function unique(): static
    {
        return $this->addOperation(new Unique());
    }

    /**
     * @return static<T>
     */
    public function chunk(int $size): static
    {
        return $this->addOperation(new Chunk($size));
    }

    /**
     * @param callable(T, T): int $comparison
     * 
     * @return static<T>
     */
    public function sort(callable $comparison): static
    {
        return $this->addOperation(new Sort($comparison));
    }
}