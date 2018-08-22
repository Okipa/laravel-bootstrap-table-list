<?php

namespace Okipa\LaravelBootstrapTableList\Tests\Unit;

use ErrorException;
use InvalidArgumentException;
use Okipa\LaravelBootstrapTableList\TableList;
use Okipa\LaravelBootstrapTableList\Test\Fakers\CompaniesFaker;
use Okipa\LaravelBootstrapTableList\Test\Fakers\RoutesFaker;
use Okipa\LaravelBootstrapTableList\Test\Fakers\UsersFaker;
use Okipa\LaravelBootstrapTableList\Test\Models\Company;
use Okipa\LaravelBootstrapTableList\Test\Models\User;
use Okipa\LaravelBootstrapTableList\Test\TableListTestCase;

class ExceptionsTest extends TableListTestCase
{
    use RoutesFaker;
    use UsersFaker;
    use CompaniesFaker;

    /**
     * @expectedException ErrorException
     * @expectedExceptionMessage The table list model has not been defined or is not an instance of
     *                            Illuminate\Database\Eloquent\Model.
     */
    public function testAddColumnWithoutModelToTableList()
    {
        app(TableList::class)->addColumn('name');
    }

    /**
     * @expectedException ErrorException
     * @expectedExceptionMessage No column has been added to the table list. Please add at least one column by using
     *                            the "addColumn" method on the table list object.
     */
    public function testRenderTableListWithNoDeclaredColumn()
    {
        $routes = [
            'index' => ['alias' => 'users.index', 'parameters' => []],
        ];
        $table = app(TableList::class)->setRoutes($routes)->setModel(User::class);
        $table->render();
    }

    /**
     * @expectedException ErrorException
     * @expectedExceptionMessage The given searchable column attribute « not_existing_column » does not exist in the «
     *                            users_test » table. Set the correct column-related table and alias with the «
     *                            setCustomTable() » method.
     */
    public function testRenderTableListWithNotExistingSearchableColumn()
    {
        $routes = [
            'index' => ['alias' => 'users.index', 'parameters' => []],
        ];
        $table = app(TableList::class)->setRoutes($routes)->setModel(User::class);
        $table->addColumn('not_existing_column')->isSearchable();
        $table->render();
    }

    /**
     * @expectedException ErrorException
     * @expectedExceptionMessage The required « index » route key is missing. Use the « setRoutes() » method to declare
     *                           it.
     */
    public function testRenderTableListWithNoDeclaredRoutes()
    {
        $table = app(TableList::class)->setModel(User::class);
        $table->render();
    }

    /**
     * @expectedException ErrorException
     * @expectedExceptionMessage No columns have been chosen for the destroy confirmation. Use the «
     *                            useForDestroyConfirmation() » method on column objects to define them.
     */
    public function testRenderTableListWithDestroyRouteWithoutDestroyAttribute()
    {
        $routes = [
            'index'   => ['alias' => 'users.index', 'parameters' => []],
            'destroy' => ['alias' => 'users.destroy', 'parameters' => []],
        ];
        $table = app(TableList::class)->setRoutes($routes)->setModel(User::class);
        $table->addColumn('name')->setTitle('Name');
        $table->render();
    }

    /**
     * @expectedException ErrorException
     * @expectedExceptionMessage The « sortByDefault() » method has already been called. You only can sort a column by
     *                            default once.
     */
    public function testSortByDefaultCalledMultiple()
    {
        $table = app(TableList::class)->setModel(User::class);
        $table->addColumn('name')->sortByDefault();
        $table->addColumn('email')->sortByDefault();
    }

    /**
     * @expectedException ErrorException
     * @expectedExceptionMessage The required « index » route key is missing. Use the « setRoutes() » method to
     *                            declare it.
     */
    public function testSetRoutesErrorWithMissingIndex()
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
    public function testSetRoutesWithNotAllowedRoutes()
    {
        $routes = [
            'index'    => ['alias' => 'users.index', 'parameters' => []],
            'activate' => ['alias' => 'users.activate', 'parameters' => []],
        ];
        app(TableList::class)->setRoutes($routes);
    }

    /**
     * @expectedException InvalidArgumentException
     * @expectedExceptionMessage Invalid « $routeKey » argument for the « route() » method. The route key « create »
     *                            has not been found in the routes stack.
     */
    public function testGetRouteOnInexistantRoute()
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

    public function testSetCustomTable()
    {
        $this->createMultipleUsers(5);
        $this->createMultipleCompanies(2);
        $this->setRoutes(['users'], ['index']);
        $routes = [
            'index' => ['alias' => 'users.index', 'parameters' => []],
        ];
        $table = app(TableList::class)->setRoutes($routes)
            ->setModel(Company::class)
            ->addQueryInstructions(function($query) {
                $query->select('companies_test.*');
                $query->addSelect('users_test.name as owner');
                $query->join('users_test', 'users_test.id', '=', 'companies_test.owner_id');
            });
        //        $table->addColumn('name')->sortByDefault();
        //        $table->addColumn('email');
        $table->addColumn('owner')->setCustomTable('companies', 'name')->isSortable()->isSearchable();
        $table->render();
        dd('test');
    }
}
