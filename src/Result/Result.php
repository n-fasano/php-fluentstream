<?php

namespace Fasano\FluentStream\Result;

/**
 * @template T
 */
abstract readonly class Result
{
    /**
     * @param T $value
     */
    public function __construct(public mixed $value)
    {}

    /**
     * @param T $value
     * 
     * @return Success<T>
     */
    public static function success(mixed $value): Success
    {
        return new Success($value);
    }

    /**
     * @template E
     * 
     * @param T $value
     * @param E $description
     * 
     * @return Failure<T, E>
     */
    public static function failure(mixed $value, mixed $description): Failure
    {
        return new Failure($value, $description);
    }
}

/**
 * @template T
 * 
 * @extends Result<T>
 */
final readonly class Success extends Result
{}

/**
 * @template T
 * 
 * @extends Result<T>
 */
final readonly class Failure extends Result
{
    public function __construct(
        public mixed $value,
        public mixed $description,
    ) {}
}