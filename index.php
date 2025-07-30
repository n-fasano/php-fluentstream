<?php

use Fasano\FluentStream\Stream;

require_once './vendor/autoload.php';

/**
 * @return iterable<int>
 */
function naturalNumbers(int $beginning = 0): iterable
{
    assert($beginning >= 0);

    $i = $beginning;

    while (true) {
        yield $i++;

        if ($i >= PHP_INT_MAX) {
            return;
        }
    }
}

$stream = Stream::of(naturalNumbers(1))
    ->map(fn (int $x): int => $x ** 2)
    ->take(25)
    ->each(fn (int $x): int => print("$x\n"))
    // ->flush() # This will trigger the prints
    // ->collectArray() # This too
    // ->reduce(fn (int $acc, int $x): int => $acc + $x, 0) # And this
    ;