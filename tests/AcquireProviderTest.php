<?php

namespace Sneedus\Acquiring\Tests;
use \Illuminate\Foundation\Application;
use \Sneedus\Acquiring\Acquiring;
use \Sneedus\Acquiring\Models\Payment;

class AcquireProviderTest extends \Orchestra\Testbench\TestCase
{
    
    public function testApp()
    {
        
        $this->assertInstanceOf(Acquiring::class, $this->app->make(Acquiring::class));
    }

    protected function getPackageProviders($app)
    {
        return ['Sneedus\Acquiring\Providers\AcquiringServiceProvider'];
    }

    protected function getEnvironmentSetUp($app)
    {
        // Setup default database to use sqlite :memory:
        $app['config']->set('database.default', 'testbench');
        $app['config']->set('database.connections.testbench', [
            'driver'   => 'sqlite',
            'database' => ':memory:',
            'prefix'   => '',
        ]);
    }
}