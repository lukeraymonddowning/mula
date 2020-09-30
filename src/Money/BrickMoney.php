<?php


namespace Lukeraymonddowning\Mula\Money;


use Exception;
use Illuminate\Support\Collection;
use Lukeraymonddowning\Mula\Exceptions\UnreadableMonetaryValue;

class BrickMoney implements Money
{
    protected \Brick\Money\Money $value;

    public function create($amount, $currency = null): Money
    {
        $currency ??= config('mula.currency');

        try {
            $this->value = \Brick\Money\Money::ofMinor($amount, $currency);
        } catch (Exception $exception) {
            throw new UnreadableMonetaryValue($exception->getMessage());
        }

        return $this;
    }

    public function parse($amount, $currency = null): Money
    {
        // TODO: Implement parse() method.
    }

    public function display(bool $includeCurrency = true): string
    {
        // TODO: Implement display() method.
    }

    public function displayWithoutCurrency(): string
    {
        // TODO: Implement displayWithoutCurrency() method.
    }

    public function currency(): string
    {
        // TODO: Implement currency() method.
    }

    public function value(): string
    {
        // TODO: Implement value() method.
    }

    public function add(...$money): Money
    {
        // TODO: Implement add() method.
    }

    public function subtract(...$money): Money
    {
        // TODO: Implement subtract() method.
    }

    public function multiplyBy($multiplier): Money
    {
        // TODO: Implement multiplyBy() method.
    }

    public function divideBy($divisor): Money
    {
        // TODO: Implement divideBy() method.
    }

    public function mod(Money $divisor): Money
    {
        // TODO: Implement mod() method.
    }

    public function hasSameCurrencyAs(...$money): bool
    {
        // TODO: Implement hasSameCurrencyAs() method.
    }

    public function equals(...$money): bool
    {
        return $this->check($money, fn ($value) => $value->isEqualTo($this->value));
    }

    public function isGreaterThan(...$money): bool
    {
        // TODO: Implement isGreaterThan() method.
    }

    public function isGreaterThanOrEqualTo(...$money): bool
    {
        // TODO: Implement isGreaterThanOrEqualTo() method.
    }

    public function isLessThan(...$money): bool
    {
        // TODO: Implement isLessThan() method.
    }

    public function isLessThanOrEqualTo(...$money): bool
    {
        // TODO: Implement isLessThanOrEqualTo() method.
    }

    public function split($allocation): Collection
    {
        // TODO: Implement split() method.
    }

    public function copy(): Money
    {
        // TODO: Implement copy() method.
    }

    public function __toString()
    {
        // TODO: Implement __toString() method.
    }

    protected function check(array $money, callable $check)
    {
        return empty(Collection::make($money)->first(fn ($amount) => ! $check($amount->value)));
    }
}
