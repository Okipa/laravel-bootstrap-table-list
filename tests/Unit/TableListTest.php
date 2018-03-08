<?php

namespace Okipa\LaravelBootstrapTableList\Tests\Unit;

use ErrorException;
use Illuminate\Http\Request;
use InvalidArgumentException;
use Okipa\LaravelBootstrapTableList\TableList;
use Okipa\LaravelBootstrapTableList\Test\helpers\Routes;
use Okipa\LaravelBootstrapTableList\Test\helpers\Users;
use Okipa\LaravelBootstrapTableList\Tests\Models\User;
use Tests\TableListTestCase;
use View;

class TableListTest extends TableListTestCase
{
    use Routes;
    use Users;

    public function setUp()
    {
        parent::setUp();
        $this->instanciateFaker();
    }

    public function testSetModel()
    {
        $table = app(TableList::class)->setModel(User::class);
        $this->assertEquals(app(User::class), $table->tableModel);
    }

    public function testSetRequest()
    {
        $customRequest = app(Request::class);
        $customRequest->merge([
            'customField' => 'test',
        ]);
        $table = app(TableList::class)->setRequest($customRequest);
        $this->assertEquals($customRequest, $table->request);
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
        $this->assertEquals('http://localhost/users', $table->getRoute('index'));
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

    public function testSetRowsNumber()
    {
        $rowsNumber = 10;
        $table = app(TableList::class)->setRowsNumber($rowsNumber);
        $this->assertEquals($rowsNumber, $table->rowsNumber);
    }

    public function testEnableRowsNumberSelector()
    {
        $table = app(TableList::class)->enableRowsNumberSelector();
        $this->assertTrue($table->rowsNumberSelectorEnabled);
    }

    public function testAddQueryInstructions()
    {
        $queryClosure = function($query) {
            $query->select('users.*')->where('users.activated');
        };
        $table = app(TableList::class)->addQueryInstructions($queryClosure);
        $this->assertEquals($queryClosure, $table->queryClosure);
    }

    /**
     * @expectedException ErrorException
     * @expectedExceptionMessage  The table list model has not been defined or is not an instance of
     *                            Illuminate\Database\Eloquent\Model.
     */
    public function testAddColumnWithoutModel()
    {
        app(TableList::class)->addColumn('name');
    }

    public function testAddColumn()
    {
        $columnAttribute = 'name';
        $table = app(TableList::class)->setModel(User::class);
        $table->addColumn($columnAttribute);
        $this->assertEquals($table->columns->count(), 1);
        $this->assertEquals($table->columns->first()->tableList, $table);
        $this->assertEquals($table->columns->first()->customColumnTable, app(User::class)->getTable());
        $this->assertEquals($table->columns->first()->attribute, $columnAttribute);
    }

    public function testGetColumnsCount()
    {
        $table = app(TableList::class)->setModel(User::class);
        $table->addColumn('id');
        $table->addColumn('name');
        $table->addColumn('email');
        $this->assertEquals(3, $table->getColumnsCount());
    }

    public function testNavigationStatus()
    {
        $this->createMultipleUsers(10);
        $routes = [
            'index' => ['alias' => 'users.index', 'parameters' => []],
        ];
        $table = app(TableList::class)->setRoutes($routes)->setModel(User::class);
        $table->addColumn('name')->setTitle('Name')->sortByDefault();
        $table->render();
        $this->assertEquals($table->navigationStatus(), trans('tablelist::tablelist.tfoot.nav', [
            'start' => 1,
            'stop'  => 10,
            'total' => 10,
        ]));
    }

    public function testGetSearchableTitlesSingle()
    {
        $this->setRoutes(['users'], ['index']);
        $this->createMultipleUsers(10);
        $routes = [
            'index' => ['alias' => 'users.index', 'parameters' => []],
        ];
        $table = app(TableList::class)->setRoutes($routes)->setModel(User::class);
        $table->addColumn('name')->setTitle('Name')->sortByDefault()->isSearchable();
        $table->addColumn('email')->setTitle('Email');
        $table->render();
        $this->assertEquals('Name', $table->getSearchableTitles());
    }

    public function testGetSearchableTitlesMultiple()
    {
        $this->setRoutes(['users'], ['index']);
        $this->createMultipleUsers(10);
        $routes = [
            'index' => ['alias' => 'users.index', 'parameters' => []],
        ];
        $table = app(TableList::class)->setRoutes($routes)->setModel(User::class);
        $table->addColumn('name')->setTitle('Name')->sortByDefault()->isSearchable();
        $table->addColumn('email')->setTitle('Email')->isSearchable();
        $table->render();
        $this->assertEquals('Name, Email', $table->getSearchableTitles());
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
     * @expectedExceptionMessage  No column has been added to the table list. Please add at least one column by using
     *                            the "addColumn" method on the table list object.
     */
    public function testRenderWithNoDeclaredColum()
    {
        $routes = [
            'index' => ['alias' => 'users.index', 'parameters' => []],
        ];
        $table = app(TableList::class)->setRoutes($routes)->setModel(User::class);
        $table->render();
    }

    /**
     * @expectedException ErrorException
     * @expectedExceptionMessage  The given column attribute "not_existing_column" does not exist in the "users_test"
     *                            table.
     */
    public function testRenderWithNotExistingColumn()
    {
        $routes = [
            'index' => ['alias' => 'users.index', 'parameters' => []],
        ];
        $table = app(TableList::class)->setRoutes($routes)->setModel(User::class);
        $table->addColumn('not_existing_column');
        $table->render();
    }

    /**
     * @expectedException ErrorException
     * @expectedExceptionMessage  The given column "name" has no defined title. Please define a title by using the
     *                            "setTitle()" method on the column object.
     */
    public function testRenderWithoutColumnTitle()
    {
        $routes = [
            'index' => ['alias' => 'users.index', 'parameters' => []],
        ];
        $table = app(TableList::class)->setRoutes($routes)->setModel(User::class);
        $table->addColumn('name');
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

    /**
     * @expectedException ErrorException
     * @expectedExceptionMessage  No default column has been selected for the table sort. Please define a column sorted
     *                            by default by using the "sortByDefault()" method.
     */
    public function testRenderWithoutDefaultSortByColumn()
    {
        $routes = [
            'index' => ['alias' => 'users.index', 'parameters' => []],
        ];
        $table = app(TableList::class)->setRoutes($routes)->setModel(User::class);
        $table->addColumn('name')->setTitle('Name');
        $table->render();
    }

    public function testRenderWithOnlyIndexRouteWithEmptyList()
    {
        $routes = [
            'index' => ['alias' => 'users.index', 'parameters' => []],
        ];
        $table = app(TableList::class)->setRoutes($routes)->setModel(User::class);
        $table->addColumn('name')->setTitle('Name')->sortByDefault();
        $table->render();
        $this->assertContains(
            'Name',
            View::make('tablelist::thead', ['table' => $table])->render()
        );
        $this->assertContains(
            trans('tablelist::tablelist.tbody.empty'),
            View::make('tablelist::tbody', ['table' => $table])->render()
        );
        $this->assertContains(
            $table->navigationStatus(),
            View::make('tablelist::tfoot', ['table' => $table])->render()
        );
    }

//    public function testRenderWithAllRoutesWithFilledList()
//    {
//        $this->setRoutes(['users'], ['index', 'create', 'edit', 'destroy']);
//        $users = $this->createMultipleUsers(10);
//        $routes = [
//            'index'   => ['alias' => 'users.index', 'parameters' => []],
//            'create'  => ['alias' => 'users.create', 'parameters' => []],
//            'edit'    => ['alias' => 'users.edit', 'parameters' => []],
//            'destroy' => ['alias' => 'users.destroy', 'parameters' => []],
//        ];
//        $table = app(TableList::class)->setRoutes($routes)->setModel(User::class);
//        $table->addColumn('name')->setTitle('Name')->sortByDefault()->useForDestroyConfirmation();
//        $table->addColumn('email')->setTitle('Email')->isSearchable();
//        $table->render();
//
//        dd(route('users.index'));
//
////        dd(View::make('tablelist::thead', ['table' => $table])->render());
//
////        $this->assertContains(
////            'Name',
////            View::make('tablelist::thead', ['table' => $table])->render()
////        );
////        $this->assertContains(
////            'Email',
////            View::make('tablelist::thead', ['table' => $table])->render()
////        );
////        foreach ($users as $user) {
////            $this->assertContains(
////                $user->name,
////                View::make('tablelist::tbody', ['table' => $table])->render()
////            );
////        }
////        $this->assertContains(
////            $table->navigationStatus(),
////            View::make('tablelist::tfoot', ['table' => $table])->render()
////        );
//    }
}
