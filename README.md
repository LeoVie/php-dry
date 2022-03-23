# php-dry – Clone Detection for PHP
php-dry detects duplicated behaviour in your application, even if 
the duplicated passages are implemented completely different to each other.

## Run

### Run via Docker (recommended)
```bash
docker run -v {path_to_project}:/project leovie/php-dry -h
```

### Run via binary
```bash
vendor/bin/php-dry -h
```

## Theoretical background
(Practically) every application source code contains clones.
A clone has multiple clone instances, which are similar logic at different
locations in the source code.

The existence of clones breaks the [DRY principle](https://en.wikipedia.org/wiki/Don%27t_repeat_yourself)
and increases the effort for testing and maintaining the application.
Another disadvantage of having many clones in your application is that
there is more code that you or other contributors have to read and
understand.

### Types of clones
There are four different types of clones.

#### Type-1 clones
Instances of a type-1 clone can differ in comments, spaces and layout.

Example: The following two functions are instances of a type-1 clone.
```php
function numberIsEven(int $number): bool
{
    $isEven = ($number % 2) === 0;
    
    return $isEven;
}
```
```php
function numberIsEven(int $number): bool
{
    // Use modulo to check if number is
    // dividable by 2
    $isEven 
        = ($number % 2) === 0;
    
    return $isEven;
}
```

#### Type-2 clones
Instances of a type-2 clone have all characteristics of type-1 clones and
can additionally differ in the names of identifiers and in literals.

Example: The following two functions are instances of a type-2 clone.
```php
function numberIsEven(int $number): bool
{
    $isEven = ($number % 2) === 0;
    
    return $isEven;
}
```
```php
function numberIsNotOdd(int $number): bool
{
    // Use modulo to check if number is
    // dividable by 2
    $isNotOdd 
        = ($number % 2) === 0;
    
    return $isNotOdd;
}
```

#### Type-3 clones
Instances of a type-3 clone have all characteristics of type-2 clones.
Furthermore, there can be added or removed statements in one of the clone
instances.

Example: The following two functions are instances of a type-3 clone.
```php
function numberIsEven(int $number): bool
{
    $isEven = ($number % 2) === 0;
    
    return $isEven;
}
```
```php
function numberIsNotOdd(int $number): bool
{
    // Use modulo to check if number is
    // dividable by 2
    $isNotOdd 
        = ($number % 2) === 0;
    print($isNotOdd);
    
    return $isNotOdd;
}
```

#### Type-4 clones
Instances of a type-4 clone can differ completely in their syntax. Only
the semantic has to stay the same between the clone instances.

Example: The following two functions are instances of a type-4 clone.
```php
function numberIsEven(int $number): bool
{
    $isEven = ($number % 2) === 0;
    
    return $isEven;
}
```
```php
function isNumberValid(int $a): bool
{
    $half = 0.5 * $a;
    
    return floor($half) === ceil($half);
}
```

## Thanks
Special thank you belongs to [queo GmbH](https://www.queo.de) for sponsoring
the development and maintenance of php-dry.