<?php

namespace Lukeraymonddowning\Mula;

use Lukeraymonddowning\Mula\Money\Money;

class Mula
{
    public function create($amount, $currency = null): Money
    {
        return app(Money::class)->create($amount, $currency);
    }

    public function parse($amount, $currency = null): Money
    {
        return app(Money::class)->parse($amount, $currency);
    }
}
