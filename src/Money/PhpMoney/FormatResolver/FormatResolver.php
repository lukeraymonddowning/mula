<?php

namespace Lukeraymonddowning\Mula\Money\PhpMoney\FormatResolver;

use Money\MoneyFormatter;

interface FormatResolver
{
    public function resolve(bool $includeCurrency): MoneyFormatter;
}
