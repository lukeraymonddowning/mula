<?php

namespace Lukeraymonddowning\Mula\Facades;

use Illuminate\Support\Facades\Facade;
use Lukeraymonddowning\Mula\Money\Money;

/**
 * @method static Money create($amount, $currency = null)
 * @method static Money parse($amount, $currency = null)
 *
 * @see \Lukeraymonddowning\Mula\Money\Money
 */
class Mula extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'mula';
    }
}
