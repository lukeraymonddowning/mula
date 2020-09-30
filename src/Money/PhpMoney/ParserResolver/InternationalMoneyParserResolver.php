<?php


namespace Lukeraymonddowning\Mula\Money\PhpMoney\ParserResolver;


use Lukeraymonddowning\Mula\Money\PhpMoney;
use Money\Currencies\ISOCurrencies;
use Money\MoneyParser;
use Money\Parser\IntlMoneyParser;
use NumberFormatter;

class InternationalMoneyParserResolver implements ParserResolver
{
    public function resolve(): MoneyParser
    {
        $numberFormatter = new NumberFormatter(PhpMoney::locale(), NumberFormatter::CURRENCY);
        return new IntlMoneyParser($numberFormatter, new ISOCurrencies);
    }
}
