<?php

use Lukeraymonddowning\Mula\Money\PhpMoney\FormatResolver\InternationalMoneyFormatResolver;
use Lukeraymonddowning\Mula\Money\PhpMoney\ParserResolver\AggregateMoneyParserResolver;
use Lukeraymonddowning\Mula\Money\PhpMoney\ParserResolver\DecimalMoneyParserResolver;
use Lukeraymonddowning\Mula\Money\PhpMoney\ParserResolver\InternationalMoneyParserResolver;

return [
    'default' => env('MULA_DRIVER', 'phpmoney'),
    'options' => [
        'brick' => [
            'driver' => Lukeraymonddowning\Mula\Money\BrickMoney::class,
            'rounding' => Brick\Math\RoundingMode::DOWN,
        ],
        'phpmoney' => [
            'driver' => Lukeraymonddowning\Mula\Money\PhpMoney::class,
            'locale' => 'en_US',
            'formatter' => [
                'default' => 'international',
                'options' => [
                    'international' => [
                        'driver' => InternationalMoneyFormatResolver::class,
                    ],
                ],
            ],
            'parser' => [
                'default' => 'aggregate',
                'options' => [
                    'aggregate' => [
                        'driver' => AggregateMoneyParserResolver::class,
                    ],
                    'international' => [
                        'driver' => InternationalMoneyParserResolver::class,
                    ],
                    'decimal' => [
                        'driver' => DecimalMoneyParserResolver::class,
                    ],
                ],
            ],
        ],
    ],

    'currency' => env('MULA_CURRENCY', 'USD'),
];
