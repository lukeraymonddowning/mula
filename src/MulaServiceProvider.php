<?php

namespace Lukeraymonddowning\Mula;

use Illuminate\Support\Collection;
use Illuminate\Support\ServiceProvider;
use Lukeraymonddowning\Mula\Money\Money;
use Lukeraymonddowning\Mula\Money\PhpMoney\FormatResolver\FormatResolver;
use Lukeraymonddowning\Mula\Money\PhpMoney\ParserResolver\ParserResolver;

class MulaServiceProvider extends ServiceProvider
{
    public function boot()
    {
        if (! $this->app->runningInConsole()) {
            return;
        }

        $this->publishes([__DIR__.'/../config/mula.php' => config_path('mula.php')], 'config');
    }

    public function register()
    {
        $this->mergeConfigFrom(__DIR__.'/../config/mula.php', 'mula');

        $this->app->bind(Money::class, fn() => app(data_get(config('mula.options'), config('mula.default'))['driver']));
        $this->app->singleton('mula', Mula::class);

        $this->bindPhpMoney();
        $this->addCollectionMacros();
    }

    protected function bindPhpMoney()
    {
        $this->app->bind(
            FormatResolver::class,
            data_get(
                config('mula.options.phpmoney.formatter.options'),
                config('mula.options.phpmoney.formatter.default')
            )['driver']
        );

        $this->app->bind(
            ParserResolver::class,
            data_get(
                config('mula.options.phpmoney.parser.options'),
                config('mula.options.phpmoney.parser.default')
            )['driver']
        );
    }

    public function addCollectionMacros()
    {
        Collection::macro(
            'financialSum',
            fn () => $this
                ->filter(fn ($item) => $item instanceof Money)
                ->reduce(fn ($carry, $money) => $carry ? $carry->add($money) : $money)
        );
    }
}
