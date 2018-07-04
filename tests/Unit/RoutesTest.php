<?php

namespace Okipa\LaravelBootstrapTableList\Tests\Unit;

use Okipa\LaravelBootstrapTableList\TableList;
use Okipa\LaravelBootstrapTableList\Test\Fakers\RoutesFaker;
use Okipa\LaravelBootstrapTableList\Test\Fakers\UsersFaker;
use Okipa\LaravelBootstrapTableList\Test\Models\User;
use Okipa\LaravelBootstrapTableList\Test\TableListTestCase;

class RoutesTest extends TableListTestCase
{
    use RoutesFaker;
    use UsersFaker;

    public function testSetRoutesSuccess()
    {
        $routes = [
            'index'   => ['alias' => 'users.index', 'parameters' => []],
            'create'  => ['alias' => 'users.create', 'parameters' => []],
            'edit'    => ['alias' => 'users.edit', 'parameters' => []],
            'destroy' => ['alias' => 'users.destroy', 'parameters' => []],
        ];
        $table = app(TableList::class)->setRoutes($routes);
        $this->assertEquals($routes, $table->routes);
    }

    /**
     * @expectedException ErrorException
     * @expectedExceptionMessage  The required "index" route key is missing. Please use the setRoutes() method to
     *                            declare it.
     */
    public function testSetRoutesErrorMissingIndex()
    {
        $routes = [
            'create'  => ['alias' => 'users.create', 'parameters' => []],
            'edit'    => ['alias' => 'users.edit', 'parameters' => []],
            'destroy' => ['alias' => 'users.destroy', 'parameters' => []],
        ];
        app(TableList::class)->setRoutes($routes);
    }

    /**
     * @expectedException ErrorException
     * @expectedExceptionMessage  The "alias" key is missing from the "create" route definition. Each route key must
     *                            contain an array with a (string) "alias" key and a (array) "parameters" value. Check
     *                            the following example : ["index" => ["alias" => "news.index","parameters" => []].
     *                            Please correct your routes declaration using the setRoutes() method.
     */
    public function testSetRoutesErrorWrongStructure()
    {
        $routes = [
            'index'  => ['alias' => 'users.index', 'parameters' => []],
            'create' => ['test' => 'test'],
        ];
        app(TableList::class)->setRoutes($routes);
    }

    /**
     * @expectedException ErrorException
     * @expectedExceptionMessage  The "activate" key is not an authorized route key (index, create, edit, destroy).
     *                            Please correct your routes declaration using the setRoutes() method.
     */
    public function testSetRoutesErrorNotAllowedRoutes()
    {
        $routes = [
            'index'    => ['alias' => 'users.index', 'parameters' => []],
            'activate' => ['alias' => 'users.activate', 'parameters' => []],
        ];
        app(TableList::class)->setRoutes($routes);
    }

    public function testGetRouteSuccess()
    {
        $this->setRoutes(['users'], ['index']);
        $routes = [
            'index'   => ['alias' => 'users.index', 'parameters' => []],
            'create'  => ['alias' => 'users.create', 'parameters' => []],
            'edit'    => ['alias' => 'users.edit', 'parameters' => []],
            'destroy' => ['alias' => 'users.destroy', 'parameters' => []],
        ];
        $table = app(TableList::class)->setRoutes($routes);
        $this->assertEquals('http://localhost/users/index', $table->getRoute('index'));
    }

    /**
     * @expectedException InvalidArgumentException
     * @expectedExceptionMessage  Invalid $routeKey argument for the route() method. The route key «create» has not
     *                            been found in the routes stack.
     */
    public function testGetRouteDoesNotExistInRouteStack()
    {
        $routes = [
            'index' => ['alias' => 'users.index', 'parameters' => []],
        ];
        $table = app(TableList::class)->setRoutes($routes);
        $table->getRoute('create');
    }

    /**
     * @expectedException InvalidArgumentException
     * @expectedExceptionMessage  Invalid $routeKey argument for the route() method. The route key «update» has not
     *                            been found in the routes stack.
     */
    public function testGetRouteWithEmptyRouteStack()
    {
        app(TableList::class)->getRoute('update');
    }

    public function testIsRouteDefined()
    {
        $routes = [
            'index' => ['alias' => 'users.index', 'parameters' => []],
        ];
        $table = app(TableList::class)->setRoutes($routes);
        $this->assertTrue($table->isRouteDefined('index'));
        $this->assertFalse($table->isRouteDefined('update'));
    }

    /**
     * @expectedException ErrorException
     * @expectedExceptionMessage  The required "index" route key is missing. Please use the setRoutes() method to
     *                            declare it.
     */
    public function testRenderWithNoDeclaredRoutes()
    {
        $table = app(TableList::class)->setModel(User::class);
        $table->render();
    }

    /**
     * @expectedException ErrorException
     * @expectedExceptionMessage  No column attribute has been choosed for the destroy confirmation. Please define an
     *                            attribute by using the "useForDestroyConfirmation()" method on a column object.
     */
    public function testRenderWithDestroyRouteWithoutDestroyAttribute()
    {
        $routes = [
            'index'   => ['alias' => 'users.index', 'parameters' => []],
            'destroy' => ['alias' => 'users.destroy', 'parameters' => []],
        ];
        $table = app(TableList::class)->setRoutes($routes)->setModel(User::class);
        $table->addColumn('name')->setTitle('Name');
        $table->render();
    }
}