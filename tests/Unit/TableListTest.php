<?php

namespace Okipa\LaravelBootstrapTableList\Tests\Unit;

use ErrorException;
use Illuminate\Http\Request;
use InvalidArgumentException;
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

    public function testTableTitleHtml()
    {
        $routes = [
            'index' => ['alias' => 'users.index', 'parameters' => []],
        ];
        $table = app(TableList::class)->setRoutes($routes)->setModel(User::class);
        $table->addColumn('name')->setTitle('Name')->sortByDefault();
        $table->addColumn('email')->setTitle('Email');
        $table->render();
        $thead = View::make('tablelist::thead', ['table' => $table])->render();
        $this->assertContains('Name', $thead);
        $this->assertContains('Email', $thead);
    }

    public function testNavigationStatusHtml()
    {
        $routes = [
            'index' => ['alias' => 'users.index', 'parameters' => []],
        ];
        $table = app(TableList::class)->setRoutes($routes)->setModel(User::class);
        $table->addColumn('name')->setTitle('Name')->sortByDefault();
        $table->render();
        $tfoot = View::make('tablelist::tfoot', ['table' => $table])->render();
        $this->assertContains($table->navigationStatus(), $tfoot);
    }

    public function testEmptyListHtml()
    {
        $routes = [
            'index' => ['alias' => 'users.index', 'parameters' => []],
        ];
        $table = app(TableList::class)->setRoutes($routes)->setModel(User::class);
        $table->addColumn('name')->setTitle('Name')->sortByDefault();
        $table->render();
        $tbody = View::make('tablelist::tbody', ['table' => $table])->render();
        $this->assertContains(trans('tablelist::tablelist.tbody.empty'), $tbody);
    }

    public function testFilledListHtml()
    {
        $users = $this->createMultipleUsers(5);
        $routes = [
            'index' => ['alias' => 'users.index', 'parameters' => []],
        ];
        $table = app(TableList::class)->setRoutes($routes)->setModel(User::class);
        $table->addColumn('name')->setTitle('Name')->sortByDefault();
        $table->addColumn('email')->setTitle('Email');
        $table->render();
        $tbody = View::make('tablelist::tbody', ['table' => $table])->render();
        foreach ($users as $user) {
            $this->assertContains($user->name, $tbody);
            $this->assertContains($user->email, $tbody);
        }
    }

    public function testNoCreateActionHtml()
    {
        $this->setRoutes(['users'], ['create']);
        $routes = [
            'index' => ['alias' => 'users.index', 'parameters' => []],
        ];
        $table = app(TableList::class)->setRoutes($routes)->setModel(User::class);
        $table->addColumn('name')->setTitle('Name')->sortByDefault();
        $table->render();
        // tfoot
        $tfoot = View::make('tablelist::tfoot', ['table' => $table])->render();
        $this->assertNotContains('<div class="tfoot-tab col-sm-4 create-button">', $tfoot);
        $this->assertNotContains('href="http://localhost/users/create"', $tfoot);
        $this->assertNotContains('title="Add"', $tfoot);
    }

    public function testCreateActionHtml()
    {
        $this->setRoutes(['users'], ['create']);
        $routes = [
            'index'  => ['alias' => 'users.index', 'parameters' => []],
            'create' => ['alias' => 'users.create', 'parameters' => []],
        ];
        $table = app(TableList::class)->setRoutes($routes)->setModel(User::class);
        $table->addColumn('name')->setTitle('Name')->sortByDefault();
        $table->render();
        // tfoot
        $tfoot = View::make('tablelist::tfoot', ['table' => $table])->render();
        $this->assertContains('<div class="tfoot-tab col-sm-4 create-button">', $tfoot);
        $this->assertContains('href="http://localhost/users/create"', $tfoot);
        $this->assertContains('title="Add"', $tfoot);
    }

    public function testNoEditActionHtml()
    {
        $users = $this->createMultipleUsers(5);
        $routes = [
            'index' => ['alias' => 'users.index', 'parameters' => []],
        ];
        $table = app(TableList::class)->setRoutes($routes)->setModel(User::class);
        $table->addColumn('name')->setTitle('Name')->sortByDefault();
        $table->render();
        $tbody = View::make('tablelist::tbody', ['table' => $table])->render();
        foreach ($users as $user) {
            $this->assertNotContains('action="http://localhost/users/edit?id=' . $user->id . '"', $tbody);
        }
    }

    public function testEditActionHtml()
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
        $tbody = View::make('tablelist::tbody', ['table' => $table])->render();
        foreach ($users as $user) {
            $this->assertContains('action="http://localhost/users/edit?id=' . $user->id . '"', $tbody);
        }
    }

    public function testNoDestroyActionHtml()
    {
        $users = $this->createMultipleUsers(5);
        $routes = [
            'index' => ['alias' => 'users.index', 'parameters' => []],
        ];
        $table = app(TableList::class)->setRoutes($routes)->setModel(User::class);
        $table->addColumn('name')->setTitle('Name')->sortByDefault()->useForDestroyConfirmation();
        $table->render();
        $tbody = View::make('tablelist::tbody', ['table' => $table])->render();
        foreach ($users as $user) {
            $this->assertNotContains('action="http://localhost/users/destroy?id=' . $user->id . '"', $tbody);
            $this->assertNotContains(trans('tablelist::tablelist.modal.question', [
                'entity' => $user->{$table->destroyAttribute},
            ]), $tbody);
        }
    }

    public function testDestroyActionHtml()
    {
        $users = $this->createMultipleUsers(5);
        $this->setRoutes(['users'], ['destroy']);
        $routes = [
            'index'   => ['alias' => 'users.index', 'parameters' => []],
            'destroy' => ['alias' => 'users.destroy', 'parameters' => []],
        ];
        $table = app(TableList::class)->setRoutes($routes)->setModel(User::class);
        $table->addColumn('name')->setTitle('Name')->sortByDefault()->useForDestroyConfirmation();
        $table->render();
        $tbody = View::make('tablelist::tbody', ['table' => $table])->render();
        foreach ($users as $user) {
            $this->assertContains('action="http://localhost/users/destroy?id=' . $user->id . '"', $tbody);
            $this->assertContains(trans('tablelist::tablelist.modal.question', [
                'entity' => $user->{$table->destroyAttribute},
            ]), $tbody);
        }
    }

    public function testNoSearchableHtml()
    {
        $this->setRoutes(['users'], ['index']);
        $routes = [
            'index' => ['alias' => 'users.index', 'parameters' => []],
        ];
        $table = app(TableList::class)->setRoutes($routes)
            ->setModel(User::class);
        $table->addColumn('name')->setTitle('Name')->sortByDefault('desc');
        $table->addColumn('email')->setTitle('Email');
        $table->render();
        $thead = View::make('tablelist::thead', ['table' => $table])->render();
        $this->assertNotContains('div class="col-sm-6 col-xs-12 search-bar">', $thead);
        $this->assertNotContains('<form role="form" method="GET" action="http://localhost/users/index">', $thead);
        $this->assertNotContains('<input type="hidden" name="rowsNumber" value="20">', $thead);
        $this->assertNotContains('<input type="hidden" name="sortBy" value="name">', $thead);
        $this->assertNotContains('<input type="hidden" name="sortDir" value="desc">', $thead);
        $this->assertNotContains('name="search"', $thead);
        $this->assertNotContains(
            'placeholder="' . trans('tablelist::tablelist.thead.search') . ' ' . $table->getSearchableTitles() . '"',
            $thead
        );
        $this->assertNotContains(
            'title="' . trans('tablelist::tablelist.thead.search') . ' ' . $table->getSearchableTitles() . '"',
            $thead
        );
    }

    public function testSearchableHtml()
    {
        $this->setRoutes(['users'], ['index']);
        $routes = [
            'index' => ['alias' => 'users.index', 'parameters' => []],
        ];
        $table = app(TableList::class)->setRoutes($routes)
            ->setModel(User::class);
        $table->addColumn('name')->setTitle('Name')->sortByDefault('desc');
        $table->addColumn('email')->setTitle('Email')->isSearchable();
        $table->render();
        $thead = View::make('tablelist::thead', ['table' => $table])->render();
        $this->assertContains('div class="col-sm-6 col-xs-12 search-bar">', $thead);
        $this->assertContains('<form role="form" method="GET" action="http://localhost/users/index">', $thead);
        $this->assertContains('<input type="hidden" name="rowsNumber" value="20">', $thead);
        $this->assertContains('<input type="hidden" name="sortBy" value="name">', $thead);
        $this->assertContains('<input type="hidden" name="sortDir" value="desc">', $thead);
        $this->assertContains('name="search"', $thead);
        $this->assertContains(
            'placeholder="' . trans('tablelist::tablelist.thead.search') . ' '
            . $table->getSearchableTitles() . '"', $thead
        );
        $this->assertContains(
            'title="' . trans('tablelist::tablelist.thead.search') . ' '
            . $table->getSearchableTitles() . '"', $thead
        );
    }

    public function testRowsNumberSelectionHtml()
    {
        $this->setRoutes(['users'], ['index']);
        $routes = [
            'index' => ['alias' => 'users.index', 'parameters' => []],
        ];
        $table = app(TableList::class)->setRoutes($routes)->setModel(User::class)->enableRowsNumberSelector();
        $table->addColumn('name')->setTitle('Name')->sortByDefault();
        $table->addColumn('email')->setTitle('Email');
        $table->render();
        $thead = View::make('tablelist::thead', ['table' => $table])->render();
        $this->assertContains('<div class="col-sm-4 col-xs-12 rows-number-selector">', $thead);
        $this->assertContains('<form role="form" method="GET" action="http://localhost/users/index">', $thead);
        $this->assertContains('<input type="hidden" name="search" value="">', $thead);
        $this->assertContains('<input type="hidden" name="sortBy" value="name">', $thead);
        $this->assertContains('<input type="hidden" name="sortDir" value="asc">', $thead);
        $this->assertContains('type="number"', $thead);
        $this->assertContains('name="rowsNumber"', $thead);
        $this->assertContains('value="20"', $thead);
        $this->assertContains('placeholder="' . trans('tablelist::tablelist.thead.rows_number') . '"', $thead);
        $this->assertContains('title="' . trans('tablelist::tablelist.thead.rows_number') . '"', $thead);
    }

    public function testRowsNumberCustomHtml()
    {
        $this->setRoutes(['users'], ['index']);
        $routes = [
            'index' => ['alias' => 'users.index', 'parameters' => []],
        ];
        $table = app(TableList::class)
            ->setRoutes($routes)
            ->setModel(User::class)
            ->enableRowsNumberSelector()
            ->setRowsNumber(15);
        $table->addColumn('name')->setTitle('Name')->sortByDefault();
        $table->addColumn('email')->setTitle('Email');
        $table->render();
        $thead = View::make('tablelist::thead', ['table' => $table])->render();
        $this->assertContains('<div class="col-sm-4 col-xs-12 rows-number-selector">', $thead);
        $this->assertContains('<form role="form" method="GET" action="http://localhost/users/index">', $thead);
        $this->assertContains('<input type="hidden" name="search" value="">', $thead);
        $this->assertContains('<input type="hidden" name="sortBy" value="name">', $thead);
        $this->assertContains('<input type="hidden" name="sortDir" value="asc">', $thead);
        $this->assertContains('type="number"', $thead);
        $this->assertContains('name="rowsNumber"', $thead);
        $this->assertContains('value="15"', $thead);
        $this->assertContains('placeholder="' . trans('tablelist::tablelist.thead.rows_number') . '"', $thead);
        $this->assertContains('title="' . trans('tablelist::tablelist.thead.rows_number') . '"', $thead);
    }

    public function testSortableColumnHtml()
    {
        $this->setRoutes(['users'], ['index']);
        $routes = [
            'index' => ['alias' => 'users.index', 'parameters' => []],
        ];
        $table = app(TableList::class)->setRoutes($routes)->setModel(User::class);
        $table->addColumn('name')->setTitle('Name')->sortByDefault()->isSortable();
        $table->addColumn('email')->setTitle('Email');
        $table->render();
        $thead = View::make('tablelist::thead', ['table' => $table])->render();
        $this->assertContains(
            '<a href="http://localhost/users/index?sortBy=name&amp;sortDir=desc&amp;rowsNumber=20"',
            $thead
        );
        $this->assertNotContains(
            '<a href="http://localhost/users/index?sortBy=email&amp;sortDir=desc&amp;rowsNumber=20"',
            $thead
        );
    }

    public function testSortByColumnRequest()
    {
        $users = $this->createMultipleUsers(5);
        $customRequest = app(Request::class);
        $customRequest->merge([
            'rowsNumber' => 20,
            'sortBy'     => 'email',
            'sortDir'    => 'desc',
        ]);
        $this->setRoutes(['users'], ['index']);
        $routes = [
            'index' => ['alias' => 'users.index', 'parameters' => []],
        ];
        $table = app(TableList::class)->setRoutes($routes)->setModel(User::class)->setRequest($customRequest);
        $table->addColumn('name')->setTitle('Name')->sortByDefault();
        $table->addColumn('email')->setTitle('Email');
        $table->render();
        $this->assertEquals($users->sortByDesc('email')->values()->toArray(), $table->list->toArray()['data']);
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

    public function testSearchAccurateRequest()
    {
        $users = $this->createMultipleUsers(5);
        $customRequest = app(Request::class);
        $searchedValue = $users->sortBy('name')->values()->first()->name;
        $customRequest->merge([
            'rowsNumber' => 20,
            'search'     => $searchedValue,
        ]);
        $this->setRoutes(['users'], ['index']);
        $routes = [
            'index' => ['alias' => 'users.index', 'parameters' => []],
        ];
        $table = app(TableList::class)->setRoutes($routes)->setModel(User::class)->setRequest($customRequest);
        $table->addColumn('name')->setTitle('Name')->sortByDefault()->isSearchable();
        $table->addColumn('email')->setTitle('Email');
        $table->render();
        $this->assertEquals(
            $users->sortBy('name')->where('name', $searchedValue)->values()->toArray(),
            $table->list->toArray()['data']
        );
    }

    public function testSearchUnaccurateRequest()
    {
        $this->createMultipleUsers(10);
        $customRequest = app(Request::class);
        $searchedValue = 'al';
        $customRequest->merge([
            'rowsNumber' => 20,
            'search'     => $searchedValue,
        ]);
        $this->setRoutes(['users'], ['index']);
        $routes = [
            'index' => ['alias' => 'users.index', 'parameters' => []],
        ];
        $table = app(TableList::class)->setRoutes($routes)->setModel(User::class)->setRequest($customRequest);
        $table->addColumn('name')->setTitle('Name')->sortByDefault();
        $table->addColumn('email')->setTitle('Email')->isSearchable();
        $table->render();
        $this->assertEquals(
            App(User::class)
                ->orderBy('name', 'asc')
                ->where('email', 'like', '%' . $searchedValue . '%')
                ->get()
                ->toArray(),
            $table->list->toArray()['data']
        );
    }

    public function testCustomConfig()
    {
        config([
            'tablelist' => [
                // default values
                'default'  => [
                    'rows_number' => 5,
                ],
                'template' => [
                    'indicator' => [
                        'sort' => [
                            'class' => 'templateIndicatorSortClass',
                            'icon'  => [
                                'asc'      => 'templateIndicatorSortIconAsc',
                                'desc'     => 'templateIndicatorSortIconDesc',
                                'unsorted' => 'templateIndicatorSortIconUnsorted',
                            ],
                        ],
                    ],
                    'button'    => [
                        'create'  => [
                            'class' => 'templateButtonCreateClass',
                            'icon'  => 'templateButtonCreateIcon',
                        ],
                        'edit'    => [
                            'class' => 'templateButtonEditClass',
                            'icon'  => 'templateButtonEditIcon',
                        ],
                        'destroy' => [
                            'class' => 'templateButtonDestroyClass',
                            'icon'  => 'templateButtonDestroyIcon',
                        ],
                        'confirm' => [
                            'class' => 'templateButtonConfirmClass',
                            'icon'  => 'templateButtonConfirmIcon',
                        ],
                        'cancel'  => [
                            'class' => 'templateButtonCancelClass',
                            'icon'  => 'templateButtonCancelIcon',
                        ],
                    ],
                ],
            ],
        ]);
        $this->createMultipleUsers(10);
        $this->setRoutes(['users'], ['index', 'create', 'edit', 'destroy']);
        $routes = [
            'index'   => ['alias' => 'users.index', 'parameters' => []],
            'create'  => ['alias' => 'users.create', 'parameters' => []],
            'edit'    => ['alias' => 'users.edit', 'parameters' => []],
            'destroy' => ['alias' => 'users.destroy', 'parameters' => []],
        ];
        $table = app(TableList::class)
            ->setRoutes($routes)
            ->setModel(User::class)
            ->enableRowsNumberSelector();
        $table->addColumn('name')
            ->setTitle('Name')
            ->sortByDefault()
            ->isSortable()
            ->isSearchable()
            ->useForDestroyConfirmation();;
        $table->addColumn('email')
            ->setTitle('Email')
            ->isSearchable()
            ->isSortable();
        $table->render();
        $html = View::make('tablelist::table', ['table' => $table])->render();
        $this->assertContains(
            '<input type="hidden" name="rowsNumber" value="' . config('tablelist.default.rows_number') . '"',
            $html
        );
        $this->assertContains(
            'class="sort ' . config('tablelist.template.indicator.sort.class') . '"',
            $html
        );
        $this->assertContains(
            config('tablelist.template.indicator.sort.icon.asc'),
            $html
        );
        $this->assertContains(
            config('tablelist.template.indicator.sort.icon.unsorted'),
            $html
        );
        $this->assertContains(
            'class="' . config('tablelist.template.button.create.class') . '"',
            $html
        );
        $this->assertContains(
            config('tablelist.template.button.create.icon'),
            $html
        );
        $this->assertContains(
            'class="' . config('tablelist.template.button.edit.class') . '"',
            $html
        );
        $this->assertContains(
            config('tablelist.template.button.edit.icon'),
            $html
        );
        $this->assertContains(
            'class="' . config('tablelist.template.button.destroy.class') . '"',
            $html
        );
        $this->assertContains(
            config('tablelist.template.button.destroy.icon'),
            $html
        );
        $this->assertContains(
            'class="' . config('tablelist.template.button.cancel.class') . '"',
            $html
        );
        $this->assertContains(
            config('tablelist.template.button.cancel.icon'),
            $html
        );
    }
}
