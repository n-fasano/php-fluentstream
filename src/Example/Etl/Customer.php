<?php

namespace Fasano\FluentStream\Example\Etl;

final readonly class Customer
{
    public function __construct(
        public string $name,
        public string $email,
        public int $age,
    ) {}
}