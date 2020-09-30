<?php


namespace Lukeraymonddowning\Mula\Money;

use Exception;
use Illuminate\Support\Collection;
use Lukeraymonddowning\Mula\Exceptions\UnreadableMonetaryValue;
use Lukeraymonddowning\Mula\Money\PhpMoney\FormatResolver\FormatResolver;
use Lukeraymonddowning\Mula\Money\PhpMoney\ParserResolver\ParserResolver;
use Money\Currency;

class PhpMoney implements Money
{
    public \Money\Money $value;
    public FormatResolver $formatResolver;
    public ParserResolver $parserResolver;

    public function __construct(FormatResolver $formatResolver, ParserResolver $parserResolver)
    {
        $this->formatResolver = $formatResolver;
        $this->parserResolver = $parserResolver;
    }

    public static function locale()
    {
        return config('mula.options.phpmoney.locale');
    }

    public function create(string $amount, $currency = null): Money
    {
        $currency ??= config('mula.currency');

        try {
            $this->value = new \Money\Money($amount, new Currency($currency));
        } catch (Exception $exception) {
            throw new UnreadableMonetaryValue($exception->getMessage());
        }

        return $this;
    }

    public function parse(string $amount, $currency = null): Money
    {
        try {
            $this->value = $this->parserResolver->resolve()->parse($amount, $currency);
        } catch (Exception $exception) {
            throw new UnreadableMonetaryValue($exception->getMessage());
        }

        return $this;
    }

    public function displayWithoutCurrency(): string
    {
        return $this->display(false);
    }

    public function display(bool $includeCurrency = true): string
    {
        return $this->formatResolver->resolve($includeCurrency)->format($this->value);
    }

    public function currency(): string
    {
        return $this->value->getCurrency();
    }

    public function value(): string
    {
        return $this->value->getAmount();
    }

    public function add(...$money): Money
    {
        return $this->alterValue(fn($instance) => $instance->value->add(...Collection::make($money)->map->value));
    }

    protected function alterValue(callable $closure)
    {
        $instance = $this->copy();
        $instance->value = $closure($instance);

        return $instance;
    }

    public function copy(): Money
    {
        return $this->newInstance($this->value);
    }

    protected function newInstance(\Money\Money $phpMoney)
    {
        $instance = app(self::class);
        $instance->value = $phpMoney;

        return $instance;
    }

    public function subtract(...$money): Money
    {
        return $this->alterValue(fn($instance) => $instance->value->subtract(...Collection::make($money)->map->value));
    }

    public function multiplyBy($multiplier): Money
    {
        return $this->alterValue(fn($instance) => $instance->value->multiply($multiplier));
    }

    public function divideBy($divisor): Money
    {
        return $this->alterValue(fn($instance) => $instance->value->divide($divisor));
    }

    public function mod(Money $divisor): Money
    {
        return $this->alterValue(fn($instance) => $instance->value->mod($divisor->value));
    }

    public function hasSameCurrencyAs(...$money): bool
    {
        return $this->check($money, fn($value) => $value->isSameCurrency($this->value));
    }

    protected function check(array $money, callable $check)
    {
        return empty(Collection::make($money)->first(fn($amount) => !$check($amount->value)));
    }

    public function equals(...$money): bool
    {
        return $this->check($money, fn($value) => $value->equals($this->value));
    }

    public function isGreaterThan(...$money): bool
    {
        return $this->check($money, fn($value) => $value->lessThan($this->value));
    }

    public function isGreaterThanOrEqualTo(...$money): bool
    {
        return $this->check($money, fn($value) => $value->lessThanOrEqual($this->value));
    }

    public function isLessThan(...$money): bool
    {
        return $this->check($money, fn($value) => $value->greaterThan($this->value));
    }

    public function isLessThanOrEqualTo(...$money): bool
    {
        return $this->check($money, fn($value) => $value->greaterThanOrEqual($this->value));
    }

    public function split($allocation): Collection
    {
        return collect($this->getAllocatedAmounts($allocation))
            ->map(fn($phpMoney) => $this->newInstance($phpMoney));
    }

    protected function getAllocatedAmounts($allocation)
    {
        if (is_int($allocation)) {
            return $this->value->allocateTo($allocation);
        }

        $allocation = is_array($allocation) ? $allocation : $allocation->toArray();

        return $this->value->allocate($allocation);
    }

    public function __toString()
    {
        return $this->display();
    }
}
