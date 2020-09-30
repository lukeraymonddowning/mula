<?php

namespace Lukeraymonddowning\Mula\Money\PhpMoney\FormatResolver;

use Lukeraymonddowning\Mula\Money\PhpMoney;
use Money\Currencies\ISOCurrencies;
use Money\Formatter\IntlMoneyFormatter;
use Money\MoneyFormatter;
use NumberFormatter;

class InternationalMoneyFormatResolver implements FormatResolver
{
    protected bool $includeCurrency;

    public function resolve(bool $includeCurrency): MoneyFormatter
    {
        $this->includeCurrency = $includeCurrency;

        return new IntlMoneyFormatter($this->numberFormatter(), new ISOCurrencies());
    }

    protected function numberFormatter()
    {
        return new NumberFormatter(PhpMoney::locale(), $this->formattingStyle());
    }

    protected function formattingStyle()
    {
        return $this->includeCurrency ? NumberFormatter::CURRENCY : NumberFormatter::DECIMAL;
    }
}
