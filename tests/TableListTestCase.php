<?php

namespace Okipa\LaravelBootstrapTableList\Test;

use Faker\Factory;
use Okipa\LaravelBootstrapTableList\Test\Fakers\CompaniesFaker;
use Okipa\LaravelBootstrapTableList\Test\Fakers\RoutesFaker;
use Okipa\LaravelBootstrapTableList\Test\Fakers\UsersFaker;
use Orchestra\Testbench\TestCase;

abstract class TableListTestCase extends TestCase
{
    protected $faker;
    use RoutesFaker;
    use UsersFaker;
    use CompaniesFaker;

    /**
     * Define environment setup.
     *
     * @param  \Illuminate\Foundation\Application $app
     *
     * @return void
     */
    protected function getEnvironmentSetUp($app)
    {
        // Setup default database to use sqlite :memory:
        $app['config']->set('database.default', 'testing');
        $app['config']->set('database.connections.testing', [
            'driver'   => 'sqlite',
            'database' => ':memory:',
            'prefix'   => '',
        ]);
    }

    /**
     * Get package providers.
     *
     * @param  \Illuminate\Foundation\Application $app
     *
     * @return array
     */
    protected function getPackageProviders($app)
    {
        return [
            'Okipa\LaravelBootstrapTableList\TableListServiceProvider',
        ];
    }

    /**
     * Setup the test environment.
     */
    protected function setUp()
    {
        parent::setUp();
        $this->loadMigrationsFrom([
            '--database' => 'testing',
            '--realpath' => realpath(__DIR__ . '/database/migrations'),
        ]);
        $this->faker = Factory::create();
    }
}
