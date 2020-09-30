<?php

namespace Lukeraymonddowning\Mula\Tests;

use Illuminate\Database\Eloquent\Model;
use Lukeraymonddowning\Mula\Casts;
use Lukeraymonddowning\Mula\Facades\Mula;
use Lukeraymonddowning\Mula\MulaServiceProvider;
use Orchestra\Testbench\TestCase;

class MulaCastTest extends TestCase
{
    /**
     * @test
     * @dataProvider castSetDataProvider
     */
    public function it_can_cast_successfully_for_storage($amount, $currency)
    {
        $money = Mula::create($amount, $currency);
        $cast = new Casts\Mula();

        $castedValue = $cast->set(new ExampleModel(), 'price', $money, []);

        $this->assertEquals("$amount|$currency", $castedValue);
    }

    public function castSetDataProvider()
    {
        return [
            ['12000', 'GBP'],
            ['3255', 'USD'],
            ['9999', 'EUR'],
        ];
    }

    /**
     * @test
     * @dataProvider castGetDataProvider
     */
    public function it_can_cast_successfully_from_storage($amount, $currency)
    {
        $cast = new Casts\Mula();
        $castedValue = $cast->get(new ExampleModel(), 'price', "$amount|$currency", []);

        $this->assertTrue(Mula::create($amount, $currency)->equals($castedValue));
    }

    public function castGetDataProvider()
    {
        return [
            ['12000', 'GBP'],
            ['3255', 'USD'],
            ['9999', 'EUR'],
        ];
    }

    protected function getPackageProviders($app)
    {
        return [MulaServiceProvider::class];
    }
}

class ExampleModel extends Model
{
    protected $casts = [
        'price' => Casts\Mula::class,
    ];
}
