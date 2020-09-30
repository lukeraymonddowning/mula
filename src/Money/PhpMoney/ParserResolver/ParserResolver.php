<?php


namespace Lukeraymonddowning\Mula\Money\PhpMoney\ParserResolver;


use Money\MoneyParser;

interface ParserResolver
{
    public function resolve(): MoneyParser;
}
