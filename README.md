# FluentStream

A PHP library for fluent, lazy stream processing with a functional programming approach. FluentStream allows you to chain operations on iterables while maintaining memory efficiency through lazy evaluation.

> [!IMPORTANT]  
> I made this for fun and may or may not work on this further. The README is AI-generated. Use at your own risk.

## Features

- **Lazy Evaluation**: Operations are only executed when you collect the results
- **Memory Efficient**: Works with generators and doesn't load entire datasets into memory
- **Fluent API**: Chain operations together for readable, expressive code
- **Type Safe**: Full PHPDoc type annotations for better IDE support
- **Extensible**: Easy to add custom operations

## Quick Start

```php
<?php

use Fasano\FluentStream\Stream;

$numbers = [1, 2, 3, 4, 5, 6, 7, 8, 9, 10];

$result = Stream::of($numbers)
    ->filter(fn($x) => $x % 2 === 0)  // Keep only even numbers
    ->map(fn($x) => $x ** 2)          // Square each number
    ->take(3)                         // Take first 3 results
    ->collectArray();                 // [4, 16, 36]
```

## Available Operations

### Transformation Operations

- **`map(callable $mutation)`** - Transform each element
- **`filter(callable $predicate)`** - Keep elements that match a condition
- **`unique()`** - Remove duplicate elements
- **`sort(callable $comparison)`** - Sort elements using a comparison function
- **`chunk(int $size)`** - Group elements into arrays of specified size

### Selection Operations

- **`take(int $amount)`** - Take the first N elements
- **`skip(int $amount)`** - Skip the first N elements

### Side Effect Operations

- **`each(callable $sideEffect)`** - Execute a function for each element (useful for debugging or logging)

### Terminal Operations

- **`collect()`** - Returns an iterable (generator)
- **`collectArray()`** - Returns an array
- **`reduce(callable $reducer, mixed $initial)`** - Reduce to a single value
- **`first()`** - Get the first element
- **`last()`** - Get the last element
- **`count()`** - Count elements
- **`isEmpty()`** - Check if stream is empty
- **`flush()`** - Execute all operations and return a new stream with results

## Examples

### Working with Large Datasets

```php
function readLargeFile(): iterable {
    $handle = fopen('large-file.txt', 'r');
    while (($line = fgets($handle)) !== false) {
        yield trim($line);
    }
    fclose($handle);
}

$processedLines = Stream::of(readLargeFile())
    ->filter(fn($line) => !empty($line))     // Skip empty lines
    ->map(fn($line) => strtoupper($line))    // Convert to uppercase
    ->unique()                               // Remove duplicates
    ->take(100)                              // Process only first 100
    ->collectArray();
```

### Infinite Sequences

```php
function naturalNumbers(int $start = 0): iterable {
    $i = $start;
    while (true) {
        yield $i++;
    }
}

$squaredEvens = Stream::of(naturalNumbers(1))
    ->filter(fn($x) => $x % 2 === 0)        // Even numbers only
    ->map(fn($x) => $x ** 2)                // Square them
    ->take(10)                              // First 10 results
    ->collectArray();                       // [4, 16, 36, 64, 100, 144, 196, 256, 324, 400]
```

### Complex Data Processing

```php
$salespeople = [
    ['name' => 'Alice Johnson', 'totalSales' => 245000, 'customerSatisfaction' => 4.8],
    ['name' => 'Bob Smith', 'totalSales' => 312000, 'customerSatisfaction' => 4.2],
    ['name' => 'Charlie Brown', 'totalSales' => 189000, 'customerSatisfaction' => 4.9],
    ['name' => 'Diana Prince', 'totalSales' => 445000, 'customerSatisfaction' => 4.7],
    ['name' => 'Eve Adams', 'totalSales' => 298000, 'customerSatisfaction' => 4.6],
    // ... imagine 1000+ more salespeople
];

// Get top 100 performers by sales, then find top 10 by customer satisfaction
$topPerformers = Stream::of($salespeople)
    ->sort(fn($a, $b) => $b['totalSales'] <=> $a['totalSales'])     // Sort by sales (desc)
    ->take(100)                                                     // Top 100 by sales
    ->sort(fn($a, $b) => $b['customerSatisfaction'] <=> $a['customerSatisfaction']) // Sort by satisfaction (desc)
    ->take(10)
    ->collectArray();
```

### Debugging with Each

```php
$result = Stream::of([1, 2, 3, 4, 5])
    ->each(fn($x) => echo "Input: $x\n")      // Debug input
    ->filter(fn($x) => $x % 2 === 0)
    ->each(fn($x) => echo "After filter: $x\n")  // Debug after filter
    ->map(fn($x) => $x * 10)
    ->each(fn($x) => echo "Final: $x\n")      // Debug final result
    ->collectArray();
```

### Chunking Data

```php
$numbers = range(1, 10);

$chunks = Stream::of($numbers)
    ->chunk(3)
    ->collectArray();
// [[1, 2, 3], [4, 5, 6], [7, 8, 9], [10]]
```

## Performance Considerations

### Lazy Evaluation

Operations are not executed until you call a terminal operation:

```php
// This doesn't execute anything yet
$stream = Stream::of(range(1, 1000000))
    ->map(fn($x) => $x * 2)
    ->filter(fn($x) => $x > 1000);

// Only now does processing happen
$result = $stream->take(10)->collectArray();
```

### Memory Efficiency

FluentStream works with PHP generators, so you can process large datasets without loading everything into memory:

```php
// This processes one element at a time, not loading all data into memory
$result = Stream::of(hugeDataset())
    ->filter($someCondition)
    ->map($transformation)
    ->take(100)
    ->collectArray();
```

### When to Use flush()

Use `flush()` when you need to execute all operations and work with the results multiple times:

```php
$processed = Stream::of($data)
    ->expensiveOperation()
    ->flush();  // Execute once and store results

$count = $processed->count();
$first = $processed->first();
$array = $processed->collectArray();
```

## Creating Custom Operations

You can extend FluentStream by implementing the `Operation` interface:

```php
use Fasano\FluentStream\Operation;

class Reverse implements Operation
{
    public function apply(iterable $input): iterable
    {
        $items = [];
        foreach ($input as $value) {
            $items[] = $value;
        }
        
        for ($i = count($items) - 1; $i >= 0; $i--) {
            yield $items[$i];
        }
    }
}

// Usage would require extending the Stream class to add a reverse() method
```

## Requirements

- PHP 8.2 or higher