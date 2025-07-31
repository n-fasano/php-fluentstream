<?php

namespace Fasano\FluentStream;

use Stringable;

/**
 * @template T
 * 
 * @extends Operations<T>
 */
readonly class Stream extends Operations
{
    /**
     * @var iterable<T> $source
     */
    public iterable $source;

    public Workflow $workflow;

    /**
     * @param iterable<T> $source
     */
    public function __construct(iterable $source, ?Workflow $workflow = null)
    {
        $this->source = $source;
        $this->workflow = $workflow ?? Workflow::create();
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

    public function through(Workflow $otherWorkflow): static
    {
        $workflow = $this->workflow->compose($otherWorkflow);

        return new static($this->source, $workflow);
    }

    /**
     * @return static<T>
     */
    protected function addOperation(Operation $operation): static
    {
        $workflow = $this->workflow->addOperation($operation);

        return new static($this->source, $workflow);
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
        
        foreach ($this->workflow->operations as $operation) {
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
     * @template K of int|Stringable
     * 
     * @param callable(T): K
     * 
     * @return array<K, array<T>>
     */
    public function partition(callable $partitioner): array
    {
        $items = [];

        foreach ($this->collect() as $item) {
            $items[$partitioner($item)][] = $item;
        }

        return $items;
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