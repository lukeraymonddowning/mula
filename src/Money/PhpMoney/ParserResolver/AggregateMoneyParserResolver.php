<?php

namespace Lukeraymonddowning\Mula\Money\PhpMoney\ParserResolver;

use Money\MoneyParser;
use Money\Parser\AggregateMoneyParser;

class AggregateMoneyParserResolver implements ParserResolver
{
    protected $includedParsers = [
        InternationalMoneyParserResolver::class,
        DecimalMoneyParserResolver::class,
    ];

    public function resolve(): MoneyParser
    {
        $parsers = collect($this->includedParsers)
            ->map(fn ($class) => app()->make($class)->resolve())
            ->toArray();

        return new AggregateMoneyParser($parsers);
    }
}
