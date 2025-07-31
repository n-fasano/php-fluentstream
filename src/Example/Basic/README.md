```php
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

$first25Squared = Workflow::create()
    ->map(fn (int $x): int => $x ** 2)
    ->take(25);

$stream = Stream::of(naturalNumbers(1))
    ->through($first25Squared)
    ->each(fn (int $x): int => print("$x\n"))
    // ->flush() # This will trigger the prints
    // ->collectArray() # This too
    // ->reduce(fn (int $acc, int $x): int => $acc + $x, 0) # And this
    ;
```