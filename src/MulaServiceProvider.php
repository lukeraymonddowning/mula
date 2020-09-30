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
        /*
         * Optional methods to load your package assets
         */
        // $this->loadTranslationsFrom(__DIR__.'/../resources/lang', 'mula');
        // $this->loadViewsFrom(__DIR__.'/../resources/views', 'mula');
        // $this->loadMigrationsFrom(__DIR__.'/../database/migrations');
        // $this->loadRoutesFrom(__DIR__.'/routes.php');

        if ($this->app->runningInConsole()) {
            $this->publishes(
                [
                    __DIR__ . '/../config/mula.php' => config_path('mula.php'),
                ],
                'config'
            );

            // Publishing the views.
            /*$this->publishes([
                __DIR__.'/../resources/views' => resource_path('views/vendor/mula'),
            ], 'views');*/

            // Publishing assets.
            /*$this->publishes([
                __DIR__.'/../resources/assets' => public_path('vendor/mula'),
            ], 'assets');*/

            // Publishing the translation files.
            /*$this->publishes([
                __DIR__.'/../resources/lang' => resource_path('lang/vendor/mula'),
            ], 'lang');*/

            // Registering package commands.
            // $this->commands([]);
        }
    }

    public function register()
    {
        $this->mergeConfigFrom(__DIR__ . '/../config/mula.php', 'mula');

        $this->app->bind(Money::class, data_get(config('mula.options'), config('mula.default'))['driver']);
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
            fn() => $this
                ->filter(fn($item) => $item instanceof Money)
                ->reduce(fn($carry, $money) => $carry ? $carry->add($money) : $money)
        );
    }
}
