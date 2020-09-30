<?php

namespace Lukeraymonddowning\Mula\Money;

use Illuminate\Support\Collection;
use Lukeraymonddowning\Mula\Exceptions\UnreadableMonetaryValue;
use Stringable;

interface Money extends Stringable
{
    /**
     * Manually create a new monetary value.
     *
     * @param string $amount The amount of money. You should omit the decimal point from this amount. Eg: '10.00' would be '1000'.
     * @param string|null $currency The ISO currency code to use. If you do not provide a value, the default currency defined in the mula config file will be used.
     * @throws UnreadableMonetaryValue
     */
    public function create(string $amount, $currency = null): Money;

    /**
     * Parse a monetary string. This is useful for transforming user input into a Money object.
     *
     * @param string $amount The amount of money. The format required depends on the money driver you are using.
     * @param string|null $currency The ISO currency code to use. Some parsers, such as the PhpMoney international parser, do not require this as it can be calculated based on the given amount.
     * @throws UnreadableMonetaryValue
     */
    public function parse(string $amount, $currency = null): Money;

    public function display(bool $includeCurrency = true): string;

    public function displayWithoutCurrency(): string;

    public function currency(): string;

    public function value(): string;

    public function add(...$money): Money;

    public function subtract(...$money): Money;

    public function multiplyBy($multiplier): Money;

    public function divideBy($divisor): Money;

    public function mod(Money $divisor): Money;

    public function hasSameCurrencyAs(...$money): bool;

    public function equals(...$money): bool;

    public function isGreaterThan(...$money): bool;

    public function isGreaterThanOrEqualTo(...$money): bool;

    public function isLessThan(...$money): bool;

    public function isLessThanOrEqualTo(...$money): bool;

    /**
     * @param int|array|Collection $allocation
     */
    public function split($allocation): Collection;

    public function copy(): Money;
}
