<?php

namespace Lukeraymonddowning\Mula\Money\PhpMoney\ParserResolver;

use Money\Currencies\ISOCurrencies;
use Money\MoneyParser;
use Money\Parser\DecimalMoneyParser;

class DecimalMoneyParserResolver implements ParserResolver
{
    public function resolve(): MoneyParser
    {
        return new DecimalMoneyParser(new ISOCurrencies());
    }
}
