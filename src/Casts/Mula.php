<?php

namespace Lukeraymonddowning\Mula\Casts;

use Illuminate\Contracts\Database\Eloquent\CastsAttributes;
use Lukeraymonddowning\Mula\Facades;

class Mula implements CastsAttributes
{
    protected $columns = [];

    public function __construct(...$columns)
    {
        $this->columns = $columns;
    }

    public function get($model, string $key, $money, array $attributes)
    {
        return Facades\Mula::create(...$this->getMonetaryValues($money, $attributes));
    }

    protected function getMonetaryValues($money, $attributes)
    {
        return $this->expectsCurrencyAndAmountInSameColumn()
            ? explode('|', $money, 2)
            : [$attributes[$this->columns[0]], $attributes[$this->columns[1]]];
    }

    protected function expectsCurrencyAndAmountInSameColumn()
    {
        return empty($this->columns);
    }

    public function set($model, string $key, $money, array $attributes)
    {
        if ($this->expectsCurrencyAndAmountInSameColumn()) {
            return "{$money->value()}|{$money->currency()}";
        }

        return [
            $this->columns[0] => $money->value(),
            $this->columns[1] => $money->currency(),
        ];
    }
}
