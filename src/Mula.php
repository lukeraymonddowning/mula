<?php

namespace Lukeraymonddowning\Mula;

use Lukeraymonddowning\Mula\Money\Money;

class Mula
{
    public function create(string $amount, $currency = null): Money
    {
        return app(Money::class)->create($amount, $currency);
    }

    public function parse(string $amount, $currency = null): Money
    {
        return app(Money::class)->parse($amount, $currency);
    }
}
