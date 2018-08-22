<?php

namespace Okipa\LaravelBootstrapTableList\Tests\Unit;

use Illuminate\Http\Request;
use Okipa\LaravelBootstrapTableList\TableList;
use Okipa\LaravelBootstrapTableList\Test\Fakers\RoutesFaker;
use Okipa\LaravelBootstrapTableList\Test\Fakers\UsersFaker;
use Okipa\LaravelBootstrapTableList\Test\Models\User;
use Okipa\LaravelBootstrapTableList\Test\TableListTestCase;
use View;

class TableListTest extends TableListTestCase
{
    use RoutesFaker;
    use UsersFaker;

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

    public function testRenderWithColumnWithAttributeAndNoTitle()
    {
        $this->setRoutes(['users'], ['index']);
        $routes = [
            'index' => ['alias' => 'users.index', 'parameters' => []],
        ];
        $table = app(TableList::class)->setRoutes($routes)->setModel(User::class);
        $table->addColumn('name')->sortByDefault()->isSearchable();
        $html = $table->render();
        $this->assertContains('validation.attributes.name', $html);
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

    public function testNavigationStatusHtml()
    {
        $routes = [
            'index' => ['alias' => 'users.index', 'parameters' => []],
        ];
        $table = app(TableList::class)->setRoutes($routes)->setModel(User::class);
        $table->addColumn('name')->setTitle('Name')->sortByDefault();
        $table->render();
        $tfoot = view('tablelist::tfoot', ['table' => $table])->render();
        $this->assertContains($table->navigationStatus(), $tfoot);
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

    public function testIsRouteDefined()
    {
        $routes = [
            'index' => ['alias' => 'users.index', 'parameters' => []],
        ];
        $table = app(TableList::class)->setRoutes($routes);
        $this->assertTrue($table->isRouteDefined('index'));
        $this->assertFalse($table->isRouteDefined('update'));
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

    public function testCustomRowsNumberRequest()
    {
        $this->createMultipleUsers(20);
        $customRequest = app(Request::class);
        $customRequest->merge([
            'rowsNumber' => 10,
        ]);
        $this->setRoutes(['users'], ['index']);
        $routes = [
            'index' => ['alias' => 'users.index', 'parameters' => []],
        ];
        $table = app(TableList::class)->setRoutes($routes)->setModel(User::class)->setRequest($customRequest);
        $table->addColumn('name')->setTitle('Name')->sortByDefault();
        $table->addColumn('email')->setTitle('Email');
        $table->render();
        $this->assertEquals(
            App(User::class)->orderBy('name', 'asc')->paginate(10)->toArray()['data'],
            $table->list->toArray()['data']
        );
    }

    public function testAddDisableLinesInstructions()
    {
        $disableLinesClosure = function($model) {
            return $model->id === 1;
        };
        $disabledLinesClass = ['test-disabled-custom-class'];
        $table = app(TableList::class)->disableLines($disableLinesClosure, $disabledLinesClass);
        $this->assertEquals($disableLinesClosure, $table->disableLinesClosure);
        $this->assertEquals($disabledLinesClass, $table->disableLinesClass);
    }

    public function testAddHighlightedLinesInstructions()
    {
        $highlightLinesClosure = function($model) {
            return $model->id === 1;
        };
        $highlightedLinesClass = ['test-highlighted-custom-class'];
        $table = app(TableList::class)->highlightLines($highlightLinesClosure, $highlightedLinesClass);
        $this->assertEquals($highlightLinesClosure, $table->highlightLinesClosure);
        $this->assertEquals($highlightedLinesClass, $table->highlightLinesClass);
    }
    
    // todo : test custom table field is sortable
    // todo : test custom table field is searchable
}
