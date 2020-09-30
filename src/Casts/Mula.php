<?php

namespace Lukeraymonddowning\Mula\Casts;

use Illuminate\Contracts\Database\Eloquent\CastsAttributes;
use Illuminate\Support\Str;
use Lukeraymonddowning\Mula\Facades;

class Mula implements CastsAttributes
{
    public function get($model, string $key, $money, array $attributes)
    {
        return Facades\Mula::create(Str::before($money, '|'), Str::after($money, '|'));
    }

    public function set($model, string $key, $money, array $attributes)
    {
        return "{$money->value()}|{$money->currency()}";
    }
}
