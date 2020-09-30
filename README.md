# Mula

[![Latest Version on Packagist](https://img.shields.io/packagist/v/lukeraymonddowning/mula.svg?style=flat-square)](https://packagist.org/packages/lukeraymonddowning/mula)
![Mula PhpUnit Tests](https://github.com/lukeraymonddowning/mula/workflows/Mula%20PhpUnit%20Tests/badge.svg?branch=master)
[![Quality Score](https://img.shields.io/scrutinizer/g/lukeraymonddowning/mula.svg?style=flat-square)](https://scrutinizer-ci.com/g/lukeraymonddowning/mula)

Mula is a [Laravel](https://laravel.com) package that makes it super easy to work with money in your applications.
It uses [Money for PHP](http://moneyphp.org/en/stable/index.html) under the hood, but takes away all the complexity 
and provides all the pieces you need to be up and running with money in a matter of minutes.

Mula is also fully immutable, so you won't run in to issues changing values you really wish you hadn't. It also handles
rounding and allocation, so you don't have to worry about any finances going missing.

## TOC

- [Installation](#installation)
- [Basic usage and API](#basic-usage-and-api)
    * [Create](#create)
    * [Parse](#parse)
    * [Display](#display)
    * [Display without currency](#display-without-currency)
    * [Currency](#currency)
    * [Value](#value)
    * [Add](#add)
    * [Subtract](#subtract)
    * [Multiply](#multiply-by)
    * [Divide](#divide-by)
    * [Modulus](#modulus)
    * [Has same currency](#has-same-currency-as)
    * [Equals](#equals)
    * [Is greater than](#is-greater-than)
    * [Is less than](#is-less-than)
    * [Splits and allocation](#split)
- [Storing money in a database](#storing-money-in-a-database)
- [Collection methods](#collection-methods)
    * [Financial sum](#financial-sum)

## Installation

You can install the package via composer:

```bash
composer require lukeraymonddowning/mula
```

You should publish the `mula.php` config file by running:

```bash
php artisan vendor:publish --provider="Lukeraymonddowning\Mula\MulaServiceProvider"
```

## Basic usage and API

We provide a `Mula` facade that allows you to easily create, parse and alter monetary values. 

### Create

To manually create a new instance, use the `create` method.

``` php
Mula::create('12000', 'USD'); // $120.00
```

Note that when creating, we exclude any decimal point. Currency should be passed as an [ISO 4217 code](https://en.wikipedia.org/wiki/ISO_4217).

You may also exclude the currency, in which case `Mula` will use the default currency defined in the `mula.php` config file, or your `.env` file using the `MULA_CURRENCY` key.

### Parse

More often than not, you'll want to parse existing monetary values rather than create new ones from scratch. You should
use the `parse` method when handling user input or reading monetary values from a 3rd party API.

If you are using the `phpmoney` driver (which is the default), you have a few different drivers you can use for parsing
money. You can set your desired driver in the `mula.php` config file, 
by altering the value of `mula.options.phpmoney.parser.default`.

We recommend using the default `aggregate` parser, but for the sake of clarity, we'll explain the difference between
each one. 

#### Aggregate

The `aggregate` parser (which is the default) is the most flexible driver available, and will attempt to parse monetary
strings formatted in international or decimal. Here are a few examples:

```php
Mula::parse('$120.99'); // $120.99

Mula::parse('120.99', 'USD'); // $120.99

Mula::parse('£120.99', 'USD'); // $120.99

Mula::parse('120', 'USD'); // $120.00
```

Note that in the third example, the money object is in USD, even though we parsed a value in GBP. That is because we passed a second parameter of `USD`. Passing in a currency will always override the given currency. 

#### International

The `international` parser will parse full monetary string with currency, but will not parse decimal strings.

```php
Mula::parse('$120.99'); // $120.99

Mula::parse('120.99'); // Exception
```

#### Decimal

The `decimal` parser will parse decimal values with the given currency (or the default currency), but will not parse monetary strings.

```php
Mula::parse('120.99', 'USD'); // $120.99

Mula::parse('$120.99'); // Exception
```

### Display

To display money in the UI, you can use the `display` method. It accepts a single parameter, `includeCurrency`, which
will either include or omit the currency symbol from the result.

```php
Mula::create('12099', 'USD')->display(); // $120.99

Mula::create('12099', 'USD')->display(false); // 120.99
```

`Mula` also implements the `Stringable` interface, so you can output money directly in a blade view.

```blade
@php($money = Mula::create('12099', 'USD'))
<span>{{ $money }}</span>

{-- This will show as '$120.99' --}
```

### Display without currency

As a syntactical nicety, you may use the `displayWithoutCurrency` method, which is just an alias for [`display(false)`](#display).

### Currency

The `currency` method will return the [ISO 4217 code](https://en.wikipedia.org/wiki/ISO_4217) of the money object.

```php
Mula::create('12099', 'USD')->currency(); // USD

Mula::create('12099', 'GBP')->currency(); // GBP
```

### Value

The `value` method will return the nonformatted value of the money object. You will rarely need to use this method,
but `Mula` makes use of it for casting values to the database.

```php
Mula::create('12099', 'USD')->value(); // 12099
``` 

### Add

The `add` method adds the provided money objects to the current money object and returns a new money object.
You may pass any number of money objects as varadic parameters.

```php
Mula::create('1500', 'USD')->add(Mula::create('1500', 'USD'))->display(); // $30.00

Mula::create('1500', 'USD')->add(Mula::create('1500', 'USD'), Mula::create('3000', 'USD'))->display(); // $60.00
```

### Subtract

The `add` method subtracts the provided money objects from the current money object and returns a new money object.
You may pass any number of money objects as varadic parameters.

```php
Mula::create('3000', 'USD')->subtract(Mula::create('1500', 'USD'))->display(); // $15.00

Mula::create('6000', 'USD')->subtract(Mula::create('1500', 'USD'), Mula::create('3000', 'USD'))->display(); // $15.00
```

### Multiply by

The `multiplyBy` method multiplies the money object by the given multiplier and returns a new money object.

```php
Mula::create('5000', 'USD')->multiplyBy(2)->display(); // $100.00
```

### Divide by

The `divideBy` method divides the money object by the given divisor and returns a new money object.

```php
Mula::create('5000', 'USD')->divideBy(2)->display(); // $25.00
```

### Modulus

The `mod` method returns the remainder of money after being divided into another sum of money.

```php
Mula::create('1000', 'USD')->mod(Mula::create('300', 'USD'))->display(); // $1.00
```

### Has same currency as

To check if a money object has the same currency as other money objects, use the `hasSameCurrencyAs` method. It accepts
a variable number of money objects as arguments. If any of the arguments have a different currency, the method will return
`false`, otherwise it will return `true`. 

```php
Mula::create('1000', 'USD')->hasSameCurrencyAs(Mula::create('500', 'USD')); // TRUE

Mula::create('1000', 'USD')->hasSameCurrencyAs(Mula::create('500', 'GBP')); // FALSE

Mula::create('1000', 'USD')->hasSameCurrencyAs(Mula::create('500', 'USD'), Mula::create('3000', 'USD')); // TRUE

Mula::create('1000', 'USD')->hasSameCurrencyAs(Mula::create('500', 'USD'), Mula::create('3000', 'GBP')); // FALSE
```

### Equals

To check if a money object is equal to other money objects, use the `equals` method. 
It accepts a variable number of money objects as arguments. If any of the arguments have a different amount, the method will return
`false`, otherwise it will return `true`. 

```php
Mula::create('1000', 'USD')->equals(Mula::create('1000', 'USD')); // TRUE

Mula::create('1000', 'USD')->equals(Mula::create('500', 'USD')); // FALSE

Mula::create('1000', 'USD')->equals(Mula::create('1000', 'USD'), Mula::create('1000', 'USD')); // TRUE

Mula::create('1000', 'USD')->equals(Mula::create('1000', 'USD'), Mula::create('500', 'USD')); // FALSE
```

### Is greater than

The `isGreaterThan` method checks if a money object is greater than all other money objects. It returns `true` if it is,
or `false` if any of the money object provided are greater than or equal to it.

```php
Mula::create('1000', 'USD')->isGreaterThan(Mula::create('999', 'USD')); // TRUE

Mula::create('1000', 'USD')->isGreaterThan(Mula::create('1000', 'USD')); // FALSE

Mula::create('1000', 'USD')->isGreaterThan(Mula::create('1500', 'USD')); // FALSE

Mula::create('1000', 'USD')->isGreaterThan(Mula::create('999', 'USD'), Mula::create('800', 'USD')); // TRUE

Mula::create('1000', 'USD')->isGreaterThan(Mula::create('1000', 'USD'), Mula::create('500', 'USD')); // FALSE
``` 

### Is greater than or equal to

The `isGreaterThanOrEqualTo` method checks if a money object is greater than or equal to all other money objects. It returns `true` if it is,
or `false` if any of the money object provided are greater than it.

```php
Mula::create('1000', 'USD')->isGreaterThanOrEqualTo(Mula::create('999', 'USD')); // TRUE

Mula::create('1000', 'USD')->isGreaterThanOrEqualTo(Mula::create('1000', 'USD')); // TRUE

Mula::create('1000', 'USD')->isGreaterThanOrEqualTo(Mula::create('1500', 'USD')); // FALSE

Mula::create('1000', 'USD')->isGreaterThanOrEqualTo(Mula::create('999', 'USD'), Mula::create('800', 'USD')); // TRUE

Mula::create('1000', 'USD')->isGreaterThanOrEqualTo(Mula::create('1000', 'USD'), Mula::create('500', 'USD')); // TRUE

Mula::create('1000', 'USD')->isGreaterThanOrEqualTo(Mula::create('1000', 'USD'), Mula::create('1500', 'USD')); // FALSE
``` 

### Is less than

The `isLessThan` method checks if a money object is less than all other money objects. It returns `true` if it is,
or `false` if any of the money object provided are less than or equal to it.

```php
Mula::create('1000', 'USD')->isLessThan(Mula::create('999', 'USD')); // FALSE

Mula::create('1000', 'USD')->isLessThan(Mula::create('1000', 'USD')); // TRUE

Mula::create('1000', 'USD')->isLessThan(Mula::create('1500', 'USD')); // TRUE

Mula::create('1000', 'USD')->isLessThan(Mula::create('1500', 'USD'), Mula::create('800', 'USD')); // FALSE

Mula::create('1000', 'USD')->isLessThan(Mula::create('1200', 'USD'), Mula::create('1500', 'USD')); // TRUE
``` 

### Is less than or equal to

The `isLessThanOrEqualTo` method checks if a money object is less than or equal to all other money objects. It returns `true` if it is,
or `false` if any of the money object provided are less than it.

```php
Mula::create('1000', 'USD')->isLessThanOrEqualTo(Mula::create('999', 'USD')); // FALSE

Mula::create('1000', 'USD')->isLessThanOrEqualTo(Mula::create('1000', 'USD')); // TRUE

Mula::create('1000', 'USD')->isLessThanOrEqualTo(Mula::create('1500', 'USD')); // TRUE

Mula::create('1000', 'USD')->isLessThanOrEqualTo(Mula::create('999', 'USD'), Mula::create('800', 'USD')); // FALSE

Mula::create('1000', 'USD')->isLessThanOrEqualTo(Mula::create('1000', 'USD'), Mula::create('500', 'USD')); // FALSE

Mula::create('1000', 'USD')->isLessThanOrEqualTo(Mula::create('1000', 'USD'), Mula::create('1500', 'USD')); // TRUE
``` 

### Split

Split is rather special. It allocates money based on the provided allocation. It can accept an `integer`, `array` or 
`Collection` and returns a `Collection` of `Money`.

If you want to split a money object as evenly as possible between a given number, pass an `integer`.

```php
Mula::create('10000', 'USD')->split(3); // A Collection. The first item will have a value of $33.34 the second and third items will have a value of $33.33. 
```   

If you want to allocate money based on percentages, you may pass an `array` or `Collection` of numeric values. The
values must add up to 100.

```php
Mula::create('10000', 'USD')->split([30, 70]); // A Collection. The first item will have a value of $30.00 and the second item will have a value of $70.00.

Mula::create('10000', 'USD')->split(collect([30, 70])); // A Collection. The first item will have a value of $30.00 and the second item will have a value of $70.00.
```

## Storing money in a database

`Mula` makes it easy to store and retrieve money values from a database by providing a custom cast you can attach to your
Eloquent models.

```php
use Illuminate\Database\Eloquent\Model;
use Lukeraymonddowning\Mula\Casts\Mula;

class Product extends Model {

    protected $casts = [
        'price' => Mula::class
    ];

}
```

The column storing your monetary values in your database should be a string type. This prevents floating point errors
and also allows `Mula` to store the currency along with the value.

## Collection methods

`Mula` adds macros to Laravel Collections to make it easy to perform common monetary operations to a Collection of
money objects.

### Financial sum

If you need to add together a `Collection` of money objects, you may use the `financialSum` method. It will return a 
new money object.

```php
collect(Mula::create('1500', 'USD'), Mula::create('3000', 'USD'))->financialSum(); // A new money object with a value of $45.00.
```

### Testing

`Mula` uses PhpUnit for unit tests. You can run the test suite from the terminal: 

``` bash
composer test
```

### Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information what has changed recently.

## Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) for details.

### Security

If you discover any security related issues, please email lukeraymonddowning@gmail.com instead of using the issue tracker.

## Credits

- [Luke Downing](https://github.com/lukeraymonddowning)
- [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.

## Laravel Package Boilerplate

This package was generated using the [Laravel Package Boilerplate](https://laravelpackageboilerplate.com).
