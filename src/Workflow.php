<?php

namespace Fasano\FluentStream;

/**
 * @template T
 * 
 * @extends Operations<T>
 */
readonly class Workflow extends Operations
{
    /** @var Operation[] */
    public array $operations;

    public function __construct(Operation ...$operations)
    {
        $this->operations = $operations;
    }

    /**
     * @return static<T>
     */
    public static function create(): static
    {
        return new static();
    }

    /**
     * @return static<T>
     */
    public function addOperation(Operation $operation): static
    {
        $operations = $this->operations;
        $operations[] = $operation;

        return new static(...$operations);
    }
    
    public function compose(Workflow $otherWorkflow): static
    {
        return new static(...array_merge(
            $this->operations,
            $otherWorkflow->operations,
        ));
    }
    
    // public function apply(Stream $stream): Stream
    // {
    //     return new static(...array_merge(
    //         $this->operations,
    //         $otherWorkflow->operations,
    //     ));
    // }
}