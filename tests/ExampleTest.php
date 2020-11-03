<?php

namespace Laravel\Package\Tests;

use Orchestra\Testbench\TestCase;
use Laravel\Package\PackageServiceProvider;

class ExampleTest extends TestCase
{

    protected function getPackageProviders($app)
    {
        return [PackageServiceProvider::class];
    }

    /** @test */
    public function true_is_true()
    {
        $this->assertTrue(true);
    }
}
