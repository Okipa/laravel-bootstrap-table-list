<?php

namespace Okipa\LaravelBootstrapTableList\Tests\Unit;

use Illuminate\Http\Request;
use InvalidArgumentException;
use Okipa\LaravelBootstrapTableList\TableList;
use Okipa\LaravelBootstrapTableList\Tests\Models\User;
use Tests\TableListTestCase;

class TableListTest extends TableListTestCase
{
    public function setUp()
    {
        parent::setUp();
    }

    public function testSetModel()
    {
        $table = app(TableList::class)->setModel(User::class);
        $this->assertEquals(app(User::class), $table->tableModel);
    }

    public function testSetRequest()
    {
        $request = app(Request::class);
        $table = app(TableList::class)->setRequest($request);
        $this->assertEquals($request, $table->request);
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
     * @expectedException InvalidArgumentException
     * @expectedExceptionMessage  Invalid $routes argument for the setRoutes() method. Missing required "index" array
     *                            key.
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
     * @expectedException InvalidArgumentException
     * @expectedExceptionMessage  Invalid $routes argument for the setRoutes() method. The "activate" route key is not
     *                            an authorized key (index, create, edit, destroy).
     */
    public function testSetRoutesErrorNotAllowedRoutes()
    {
        $routes = [
            'index'    => ['alias' => 'users.index', 'parameters' => []],
            'activate' => ['alias' => 'users.activate', 'parameters' => []],
        ];
        app(TableList::class)->setRoutes($routes);
    }

    public function testGetRoute()
    {
        $routes = [
            'index'   => ['alias' => 'users.index', 'parameters' => []],
            'create'  => ['alias' => 'users.create', 'parameters' => []],
            'edit'    => ['alias' => 'users.edit', 'parameters' => []],
            'destroy' => ['alias' => 'users.destroy', 'parameters' => []],
        ];
        $table = app(TableList::class)->setRoutes($routes);
//        dd($table->routes);
        dd($table->getRoute('index'));
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
        $queryClosure = function ($query) {
            $query->select('users.*')->where('users.activated');
        };
        $table = app(TableList::class)->addQueryInstructions($queryClosure);
        $this->assertEquals($queryClosure, $table->queryClosure);
    }

    /**
     * @expectedException ErrorException
     * @expectedExceptionMessage  The table list model has not been defined or is not an instance of Illuminate\Database\Eloquent\Model.
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

//    public function testGetSearchableTitles()
//    {
//
//    }
}
