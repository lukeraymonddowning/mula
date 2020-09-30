<?php

namespace Lukeraymonddowning\Mula\Tests;

use Illuminate\Support\Collection;
use Lukeraymonddowning\Mula\Facades\Mula;
use Lukeraymonddowning\Mula\MulaServiceProvider;
use Orchestra\Testbench\TestCase;

class CollectionMethodsTest extends TestCase
{
    /** @test */
    public function it_can_add_up_a_collection_of_monetary_values()
    {
        $collection = Collection::times(5, fn () => Mula::create('1000', 'GBP'));
        $sum = $collection->financialSum();

        $this->assertTrue(Mula::create('5000', 'GBP')->equals($sum));
    }

    /** @test */
    public function it_gracefully_handles_non_monetary_values()
    {
        $collection = Collection::times(5, fn () => Mula::create('1000', 'GBP'));
        $collection = collect(['foobar'])->merge($collection);

        $sum = $collection->financialSum();

        $this->assertTrue(Mula::create('5000', 'GBP')->equals($sum));
    }

    protected function getPackageProviders($app)
    {
        return [MulaServiceProvider::class];
    }
}
