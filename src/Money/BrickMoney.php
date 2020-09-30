<?php


namespace Lukeraymonddowning\Mula\Money;


use Exception;
use Illuminate\Support\Collection;
use Lukeraymonddowning\Mula\Exceptions\UnreadableMonetaryValue;

class BrickMoney implements Money
{
    protected \Brick\Money\Money $value;
    protected $arguments = [];

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
        $currency ??= config('mula.currency');

        try {
            $this->value = \Brick\Money\Money::of($amount, $currency);
        } catch (Exception $exception) {
            throw new UnreadableMonetaryValue($exception->getMessage());
        }

        return $this;
    }

    public function display(bool $includeCurrency = true): string
    {
    }

    public function displayWithoutCurrency(): string
    {
        // TODO: Implement displayWithoutCurrency() method.
    }

    public function currency(): string
    {
        return $this->value->getCurrency();
    }

    public function value(): string
    {
        return $this->value->getMinorAmount();
    }

    public function add(...$money): Money
    {
        return $this->alterValue(
            fn($instance) => Collection
                ::make($money)
                ->reduce(fn($carry, $money) => $carry->plus($money->value, $this->roundingMode()), $instance->value)
        );
    }

    protected function alterValue(callable $closure)
    {
        $instance = $this->copy();
        $instance->value = $closure($instance);

        return $instance;
    }

    public function copy(): Money
    {
        return $this->newInstance($this->value, $this->arguments);
    }

    protected function newInstance(\Brick\Money\Money $money, $arguments = [])
    {
        $instance = app(self::class);
        $instance->value = $money;
        $instance->arguments = $arguments;

        return $instance;
    }

    protected function roundingMode()
    {
        return $this->arguments['roundingMode'] ?? config('mula.options.brick.rounding');
    }

    public function subtract(...$money): Money
    {
        return $this->alterValue(
            fn($instance) => Collection
                ::make($money)
                ->reduce(fn($carry, $money) => $carry->minus($money->value, $this->roundingMode()), $instance->value)
        );
    }

    public function multiplyBy($multiplier): Money
    {
        return $this->alterValue(fn($instance) => $instance->value->multipliedBy($multiplier, $this->roundingMode()));
    }

    public function divideBy($divisor): Money
    {
        return $this->alterValue(fn($instance) => $instance->value->dividedBy($divisor, $this->roundingMode()));
    }

    public function mod(Money $divisor): Money
    {
        $amount = $divisor->copy();

        while ($amount->value->isLessThan($this->value)) {
            $calculatedAmount = $amount->add($divisor);

            if ($calculatedAmount->value->isGreaterThanOrEqualTo($amount->value)) {
                break;
            }

            $amount = $calculatedAmount;
        }

        return $this->newInstance($this->subtract($amount)->value, $this->arguments);
    }

    public function hasSameCurrencyAs(...$money): bool
    {
        // TODO: Implement hasSameCurrencyAs() method.
    }

    public function equals(...$money): bool
    {
        return $this->check($money, fn($value) => $value->isEqualTo($this->value));
    }

    protected function check(array $money, callable $check)
    {
        return empty(Collection::make($money)->first(fn($amount) => !$check($amount->value)));
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

    public function __toString()
    {
        // TODO: Implement __toString() method.
    }

    public function withArguments(array $arguments): Money
    {
        $this->arguments = array_merge($this->arguments, $arguments);

        return $this;
    }
}
