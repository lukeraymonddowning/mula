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

    /** @test */
    public function if_parameters_are_provided_it_maps_currency_and_amount_separately()
    {
        $model = new ExampleModel();
        $model->another_price = Mula::create('12345', 'GBP');
        $attributes = $model->getAttributes();

        $this->assertEquals(['currency' => 'GBP', 'amount' => '12345'], $attributes);
    }

    /** @test */
    public function if_parameters_are_provided_it_maps_currency_and_amount_separately_when_retrieving()
    {
        $cast = new Casts\Mula('amount', 'currency');
        $castedValue = $cast->get(new ExampleModel(), 'another_price', null, ['currency' => 'GBP', 'amount' => 12345]);

        $this->assertTrue(Mula::create('12345', 'GBP')->equals($castedValue));
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
        'another_price' => Casts\Mula::class.':amount,currency',
    ];
}
