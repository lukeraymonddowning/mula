<?php


namespace Lukeraymonddowning\Mula\Money;


use Illuminate\Support\Collection;
use Lukeraymonddowning\Mula\Exceptions\UnreadableMonetaryValue;
use Stringable;

interface Money extends Stringable
{
    /**
     * @throws UnreadableMonetaryValue
     */
    public function create(string $amount, $currency = null): Money;

    /**
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
