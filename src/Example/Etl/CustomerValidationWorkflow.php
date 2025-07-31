<?php

namespace Fasano\FluentStream\Example\Etl;

use Fasano\FluentStream\Result\ResultWorkflow;

/**
 * @extends ResultWorkflow<Customer>
 */
readonly class CustomerValidationWorkflow extends ResultWorkflow
{
    public static function full(): static
    {
        return new static()
            ->validateEmail()
            ->validateName()
            ->validateAge();
    }

    public function validateEmail(): static
    {
        return $this->filter(
            fn (Customer $customer): bool => filter_var($customer->email, FILTER_VALIDATE_EMAIL) ? true : false,
            fn (Customer $customer): string => sprintf('%s is not a valid email', var_export($customer->email, true)),
        );
    }

    public function validateName(): static
    {
        return $this->filter(
            fn (Customer $customer): bool => !empty($customer->name),
            fn (Customer $customer): string => sprintf('%s is not a valid name', var_export($customer->name, true)),
        );
    }

    public function validateAge(): static
    {
        return $this->filter(
            fn (Customer $customer): bool => 18 <= $customer->age && $customer->age <= 100,
            fn (Customer $customer): string => sprintf('%d is not a valid age', $customer->age),
        );
    }
}