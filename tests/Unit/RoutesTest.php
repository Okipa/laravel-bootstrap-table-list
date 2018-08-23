<?php

namespace Okipa\LaravelBootstrapTableList\Tests\Unit;

use ErrorException;
use Okipa\LaravelBootstrapTableList\TableList;
use Okipa\LaravelBootstrapTableList\Test\Models\User;
use Okipa\LaravelBootstrapTableList\Test\TableListTestCase;

class RoutesTest extends TableListTestCase
{
    /**
     * @expectedException ErrorException
     * @expectedExceptionMessage The required « index » route key is missing. Use the « setRoutes() » method to declare
     *                           it.
     */
    public function testNoDeclaredRoutes()
    {
        $table = app(TableList::class)->setModel(User::class);
        $table->render();
    }

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
     * @expectedExceptionMessage The required « index » route key is missing. Use the « setRoutes() » method to
     *                            declare it.
     */
    public function testSetRoutesWithMissingIndex()
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
     * @expectedExceptionMessage The « alias » key is missing from the « create » route definition. Each route key
     *                            must contain an array with a (string) « alias » key and a (array) « parameters »
     *                            value. Check the following example : ["index" => ["alias" =>
     *                            "news.index","parameters" => []]. Fix your routes declaration in the « setRoutes() »
     *                            method.
     */
    public function testSetRoutesWithWrongStructure()
    {
        $routes = [
            'index'  => ['alias' => 'users.index', 'parameters' => []],
            'create' => ['test' => 'test'],
        ];
        app(TableList::class)->setRoutes($routes);
    }

    /**
     * @expectedException ErrorException
     * @expectedExceptionMessage The « activate » key is not an authorized route key (index, create, edit, destroy).
     *                            Fix your routes declaration in the « setRoutes() » method.
     */
    public function testSetRoutesWithNotAllowedKey()
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
     * @expectedExceptionMessage Invalid « $routeKey » argument for the « route() » method. The route key « create »
     *                            has not been found in the routes stack.
     */
    public function testGetRouteOnNotExistingRoute()
    {
        $routes = [
            'index' => ['alias' => 'users.index', 'parameters' => []],
        ];
        $table = app(TableList::class)->setRoutes($routes);
        $table->getRoute('create');
    }

    /**
     * @expectedException InvalidArgumentException
     * @expectedExceptionMessage Invalid « $routeKey » argument for the « route() » method. The route key « update »
     *                           has not been found in the routes stack.
     */
    public function testGetRouteWithNoDeclaredRouteStack()
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

    public function testSetCreateRouteHtml()
    {
        $this->setRoutes(['users'], ['create']);
        $routes = [
            'index'  => ['alias' => 'users.index', 'parameters' => []],
            'create' => ['alias' => 'users.create', 'parameters' => []],
        ];
        $table = app(TableList::class)->setRoutes($routes)->setModel(User::class);
        $table->addColumn('name')->setTitle('Name')->sortByDefault();
        $table->render();
        $tfoot = view('tablelist::tfoot', ['table' => $table])->render();
        $this->assertContains('<div class="create-container', $tfoot);
        $this->assertContains('href="http://localhost/users/create"', $tfoot);
        $this->assertContains('title="Add"', $tfoot);
    }

    public function testSetNoCreateRouteHtml()
    {
        $this->setRoutes(['users'], ['create']);
        $routes = [
            'index' => ['alias' => 'users.index', 'parameters' => []],
        ];
        $table = app(TableList::class)->setRoutes($routes)->setModel(User::class);
        $table->addColumn('name')->setTitle('Name')->sortByDefault();
        $table->render();
        $tfoot = view('tablelist::tfoot', ['table' => $table])->render();
        $this->assertNotContains('<div class="create-container', $tfoot);
        $this->assertNotContains('href="http://localhost/users/create"', $tfoot);
        $this->assertNotContains('title="Add"', $tfoot);
    }

    public function testSetEditRouteHtml()
    {
        $users = $this->createMultipleUsers(5);
        $this->setRoutes(['users'], ['edit']);
        $routes = [
            'index' => ['alias' => 'users.index', 'parameters' => []],
            'edit'  => ['alias' => 'users.edit', 'parameters' => []],
        ];
        $table = app(TableList::class)->setRoutes($routes)->setModel(User::class);
        $table->addColumn('name')->setTitle('Name')->sortByDefault();
        $table->render();
        $tbody = view('tablelist::tbody', ['table' => $table])->render();
        foreach ($users as $user) {
            $this->assertContains('<form class="edit-' . $user->id, $tbody);
            $this->assertContains('action="http://localhost/users/edit?id=' . $user->id . '"', $tbody);
        }
    }

    public function testSetNoEditRouteHtml()
    {
        $users = $this->createMultipleUsers(5);
        $routes = [
            'index' => ['alias' => 'users.index', 'parameters' => []],
        ];
        $table = app(TableList::class)->setRoutes($routes)->setModel(User::class);
        $table->addColumn('name')->setTitle('Name')->sortByDefault();
        $table->render();
        $tbody = view('tablelist::tbody', ['table' => $table])->render();
        foreach ($users as $user) {
            $this->assertNotContains('<form class="edit-' . $user->id, $tbody);
            $this->assertNotContains('action="http://localhost/users/edit?id=' . $user->id . '"', $tbody);
        }
    }

    public function testSetDestroyRouteHtml()
    {
        $users = $this->createMultipleUsers(5);
        $this->setRoutes(['users'], ['destroy']);
        $routes = [
            'index' => ['alias' => 'users.index', 'parameters' => []],
            'destroy'  => ['alias' => 'users.destroy', 'parameters' => []],
        ];
        $table = app(TableList::class)->setRoutes($routes)->setModel(User::class);
        $table->addColumn('name')->setTitle('Name')->sortByDefault()->useForDestroyConfirmation();
        $table->render();
        $tbody = view('tablelist::tbody', ['table' => $table])->render();
        foreach ($users as $user) {
            $this->assertContains('<form class="destroy-' . $user->id, $tbody);
            $this->assertContains('action="http://localhost/users/destroy?id=' . $user->id . '"', $tbody);
        }
    }

    public function testSetNoDestroyRouteHtml()
    {
        $users = $this->createMultipleUsers(5);
        $routes = [
            'index' => ['alias' => 'users.index', 'parameters' => []],
        ];
        $table = app(TableList::class)->setRoutes($routes)->setModel(User::class);
        $table->addColumn('name')->setTitle('Name')->sortByDefault()->useForDestroyConfirmation();
        $table->render();
        $tbody = view('tablelist::tbody', ['table' => $table])->render();
        foreach ($users as $user) {
            $this->assertNotContains('<form class="destroy-' . $user->id, $tbody);
            $this->assertNotContains('action="http://localhost/users/destroy?id=' . $user->id . '"', $tbody);
        }
    }
}
