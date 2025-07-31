```php
$customers = [
    new Customer('John', 'john.doe@example.com', 20),
    new Customer('Jane', 'jane.doe@example.com', 17),
    new Customer('Methuselah', 'methuselah.doe@example.com', 969),
];

$partitions = Stream::of($customers)
    ->through(CustomerValidationWorkflow::full())
    ->partition(fn (Result $x): string => $x instanceof Success ? 'success' : 'failure');

echo json_encode($partitions, JSON_PRETTY_PRINT);
```

```json
{
    "success": [
        {
            "value": {
                "name": "John",
                "email": "john.doe@example.com",
                "age": 20
            }
        }
    ],
    "failure": [
        {
            "value": {
                "name": "Jane",
                "email": "jane.doe@example.com",
                "age": 17
            },
            "description": "17 is not a valid age"
        },
        {
            "value": {
                "name": "Methuselah",
                "email": "methuselah.doe@example.com",
                "age": 969
            },
            "description": "969 is not a valid age"
        }
    ]
}
```