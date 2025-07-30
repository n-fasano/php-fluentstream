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
 * @template T
 */
readonly class Stream
{
    /**
     * @var iterable<T> $source
     */
    public iterable $source;

    /** @var Operation[] */
    public array $operations;

    /**
     * @param iterable<T> $source
     */
    public function __construct(iterable $source, Operation ...$operations)
    {
        $this->source = $source;
        $this->operations = $operations;
    }

    /**
     * @param iterable<T> $source
     * 
     * @return static<T>
     */
    public static function of(iterable $source): static
    {
        return new static($source);
    }

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

    /**
     * @return static<T>
     */
    protected function addOperation(Operation $operation): static
    {
        $operations = $this->operations;
        $operations[] = $operation;

        return new static($this->source, ...$operations);
    }

    /**
     * @param callable(T, T): T $reducer
     * @param T $initial
     * 
     * @return T
     */
    public function reduce(callable $reducer, mixed $initial): mixed
    {
        $accumulator = $initial;

        foreach ($this->collect() as $value) {
            $accumulator = $reducer($accumulator, $value);
        }

        return $accumulator;
    }

    /**
     * @return iterable<T>
     */
    public function collect(): iterable
    {
        $current = $this->source;
        
        foreach ($this->operations as $operation) {
            $current = $operation->apply($current);
        }
        
        yield from $current;
    }

    /**
     * @return array<T>
     */
    public function collectArray(): array
    {
        return iterator_to_array($this->collect());
    }

    /**
     * @return static<T>
     */
    public function flush(): static
    {
        $results = $this->collectArray();

        return static::of($results);
    }

    /**
     * @return T|null
     */
    public function first(): mixed
    {
        foreach ($this->collect() as $value) {
            return $value;
        }
        return null;
    }

    /**
     * @return T|null
     */
    public function last(): mixed
    {
        $value = null;
        foreach ($this->collect() as $value) {
        }
        return $value;
    }

    public function count(): int
    {
        return count($this->collectArray());
    }

    public function isEmpty(): bool
    {
        foreach ($this->collect() as $value) {
            return false;
        }

        return true;
    }
}