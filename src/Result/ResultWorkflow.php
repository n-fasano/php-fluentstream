<?php

namespace Fasano\FluentStream\Result;

use Fasano\FluentStream\Workflow;

/**
 * A workflow that automatically handles Result types transparently
 * 
 * @template T
 */
readonly class ResultWorkflow extends Workflow
{
    /**
     * @inheritDoc
     */
    public function map(callable $mutation): static
    {
        return parent::map($this->wrapMap($mutation));
    }

    /**
     * @template U
     * 
     * @param callable(T): U $mutation
     * 
     * @return callable(T): Result<U>
     */
    protected function wrapMap(callable $mutation): callable
    {
        return function (mixed $value) use ($mutation): Result {
            if ($value instanceof Failure) {
                return $value;
            }

            if ($value instanceof Success) {
                $value = $value->value;
            }

            try {
                return Result::success($mutation($value));
            } catch (\Exception $e) {
                return Result::failure($value, $e->getMessage());
            }
        };
    }

    /**
     * @inheritDoc
     */
    public function filter(callable $predicate, callable $descriptor = null): static
    {
        return parent::map($this->wrapPredicate($predicate, $descriptor));
    }

    /**
     * @template E
     * 
     * @param callable(T): bool $predicate
     * 
     * @return callable(T|Result<T>): Success<T>|Failure<T, E>
     */
    protected function wrapPredicate(callable $predicate, callable $descriptor = null): callable
    {
        return function (mixed $value) use ($predicate, $descriptor): Result {
            if ($value instanceof Failure) {
                return $value;
            }

            if ($value instanceof Success) {
                $value = $value->value;
            }

            return $predicate($value)
                ? Result::success($value)
                : Result::failure($value, $descriptor($value));
        };
    }
}