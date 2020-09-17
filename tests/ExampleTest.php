<?php

namespace Lukeraymonddowning\Mula\Tests;

use Orchestra\Testbench\TestCase;
use Lukeraymonddowning\Mula\MulaServiceProvider;

class ExampleTest extends TestCase
{

    protected function getPackageProviders($app)
    {
        return [MulaServiceProvider::class];
    }
    
    /** @test */
    public function true_is_true()
    {
        $this->assertTrue(true);
    }
}
