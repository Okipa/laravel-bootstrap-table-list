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

    public function testDisableLineWithDefaultClass()
    {
        config()->set('tablelist.value.disabled_line.class', 'test-disabled-default-class');
        $this->setRoutes(['users'], ['index', 'create', 'edit', 'destroy']);
        $routes = [
            'index'   => ['alias' => 'users.index', 'parameters' => []],
            'create'  => ['alias' => 'users.create', 'parameters' => []],
            'edit'    => ['alias' => 'users.edit', 'parameters' => []],
            'destroy' => ['alias' => 'users.destroy', 'parameters' => []],
        ];
        $users = $this->createMultipleUsers(5);
        $table = app(TableList::class)->setRoutes($routes)
            ->setModel(User::class)
            ->setRoutes($routes)
            ->disableLines(function($model) use ($users) {
                return $model->id === 1 || $model->id === 2;
            });
        $table->addColumn('name')->setTitle('Name')->sortByDefault()->useForDestroyConfirmation();
        $table->addColumn('email')->setTitle('Email');
        $table->render();
        foreach ($table->list->getCollection() as $user) {
            if ($user->id === 1 || $user->id === 2) {
                $this->assertTrue($user->disabled);
            } else {
                $this->assertFalse($user->disabled);
            }
        }
        $html = view('tablelist::tbody', ['table' => $table])->render();
        $this->assertContains('test-disabled-default-class', $html);
        $this->assertContains('disabled="disabled"', $html);
        $this->assertEquals(2, substr_count($html, 'test-disabled-default-class'));
        $this->assertEquals(4, substr_count($html, 'disabled="disabled"'));
    }

    public function testDisableLineWithCustomClass()
    {
        $this->setRoutes(['users'], ['index', 'create', 'edit', 'destroy']);
        $routes = [
            'index'   => ['alias' => 'users.index', 'parameters' => []],
            'create'  => ['alias' => 'users.create', 'parameters' => []],
            'edit'    => ['alias' => 'users.edit', 'parameters' => []],
            'destroy' => ['alias' => 'users.destroy', 'parameters' => []],
        ];
        $users = $this->createMultipleUsers(5);
        $table = app(TableList::class)->setRoutes($routes)
            ->setModel(User::class)
            ->setRoutes($routes)
            ->disableLines(function($model) use ($users) {
                return $model->id === 1 || $model->id === 2;
            }, ['test-disabled-custom-class']);
        $table->addColumn('name')->setTitle('Name')->sortByDefault()->useForDestroyConfirmation();
        $table->addColumn('email')->setTitle('Email');
        $table->render();
        foreach ($table->list->getCollection() as $user) {
            if ($user->id === 1 || $user->id === 2) {
                $this->assertTrue($user->disabled);
            } else {
                $this->assertFalse($user->disabled);
            }
        }
        $html = view('tablelist::tbody', ['table' => $table])->render();
        $this->assertContains('test-disabled-custom-class', $html);
        $this->assertContains('disabled', $html);
        $this->assertContains('disabled="disabled"', $html);
        $this->assertEquals(2, substr_count($html, 'test-disabled-custom-class'));
        $this->assertEquals(14, substr_count($html, 'disabled'));
        $this->assertEquals(4, substr_count($html, 'disabled="disabled"'));
    }

    public function testWithNoDisableLines()
    {
        config()->set('tablelist.value.disabled_line.class', 'test-disabled-default-class');
        $this->setRoutes(['users'], ['index', 'create', 'edit', 'destroy']);
        $routes = [
            'index'   => ['alias' => 'users.index', 'parameters' => []],
            'create'  => ['alias' => 'users.create', 'parameters' => []],
            'edit'    => ['alias' => 'users.edit', 'parameters' => []],
            'destroy' => ['alias' => 'users.destroy', 'parameters' => []],
        ];
        $this->createMultipleUsers(5);
        $table = app(TableList::class)->setRoutes($routes)
            ->setModel(User::class)
            ->setRoutes($routes);
        $table->addColumn('name')->setTitle('Name')->sortByDefault()->useForDestroyConfirmation();
        $table->addColumn('email')->setTitle('Email');
        $table->render();
        $html = view('tablelist::tbody', ['table' => $table])->render();
        $this->assertNotContains('test-disabled-default-class', $html);
        $this->assertNotContains('disabled="disabled"', $html);
        $this->assertEquals(0, substr_count($html, 'test-disabled-default-class'));
        $this->assertEquals(0, substr_count($html, 'disabled="disabled"'));
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

    public function testHighlightLinesWithDefaultClass()
    {
        config()->set('tablelist.value.highlighted_line.class', 'test-highlighted-default-class');
        $this->setRoutes(['users'], ['index', 'create', 'edit', 'destroy']);
        $routes = [
            'index'   => ['alias' => 'users.index', 'parameters' => []],
            'create'  => ['alias' => 'users.create', 'parameters' => []],
            'edit'    => ['alias' => 'users.edit', 'parameters' => []],
            'destroy' => ['alias' => 'users.destroy', 'parameters' => []],
        ];
        $users = $this->createMultipleUsers(5);
        $table = app(TableList::class)->setRoutes($routes)
            ->setModel(User::class)
            ->setRoutes($routes)
            ->highlightLines(function($model) use ($users) {
                return $model->id === 1 || $model->id === 2;
            });
        $table->addColumn('name')->setTitle('Name')->sortByDefault()->useForDestroyConfirmation();
        $table->addColumn('email')->setTitle('Email');
        $table->render();
        foreach ($table->list->getCollection() as $user) {
            if ($user->id === 1 || $user->id === 2) {
                $this->assertTrue($user->highlighted);
            } else {
                $this->assertFalse($user->highlighted);
            }
        }
        $html = view('tablelist::table', ['table' => $table])->render();
        $this->assertContains('test-highlighted-default-class', $html);
        $this->assertEquals(2, substr_count($html, 'test-highlighted-default-class'));
    }

    public function testHighlightLinesWithCustomClass()
    {
        $this->setRoutes(['users'], ['index', 'create', 'edit', 'destroy']);
        $routes = [
            'index'   => ['alias' => 'users.index', 'parameters' => []],
            'create'  => ['alias' => 'users.create', 'parameters' => []],
            'edit'    => ['alias' => 'users.edit', 'parameters' => []],
            'destroy' => ['alias' => 'users.destroy', 'parameters' => []],
        ];
        $users = $this->createMultipleUsers(5);
        $table = app(TableList::class)->setRoutes($routes)
            ->setModel(User::class)
            ->setRoutes($routes)
            ->highlightLines(function($model) use ($users) {
                return $model->id === 1 || $model->id === 2;
            }, ['test-highlighted-custom-class']);
        $table->addColumn('name')->setTitle('Name')->sortByDefault()->useForDestroyConfirmation();
        $table->addColumn('email')->setTitle('Email');
        $table->render();
        foreach ($table->list->getCollection() as $user) {
            if ($user->id === 1 || $user->id === 2) {
                $this->assertTrue($user->highlighted);
            } else {
                $this->assertFalse($user->highlighted);
            }
        }
        $html = view('tablelist::table', ['table' => $table])->render();
        $this->assertContains('test-highlighted-custom-class', $html);
        $this->assertEquals(2, substr_count($html, 'test-highlighted-custom-class'));
    }

    public function testNoHighlightedLines()
    {
        config()->set('tablelist.value.highlighted_line.class', 'test-highlighted-default-class');
        $this->setRoutes(['users'], ['index', 'create', 'edit', 'destroy']);
        $routes = [
            'index'   => ['alias' => 'users.index', 'parameters' => []],
            'create'  => ['alias' => 'users.create', 'parameters' => []],
            'edit'    => ['alias' => 'users.edit', 'parameters' => []],
            'destroy' => ['alias' => 'users.destroy', 'parameters' => []],
        ];
        $this->createMultipleUsers(5);
        $table = app(TableList::class)->setRoutes($routes)
            ->setModel(User::class)
            ->setRoutes($routes);
        $table->addColumn('name')->setTitle('Name')->sortByDefault()->useForDestroyConfirmation();
        $table->addColumn('email')->setTitle('Email');
        $table->render();
        $html = view('tablelist::table', ['table' => $table])->render();
        $this->assertNotContains('test-highlighted-default-class', $html);
        $this->assertEquals(0, substr_count($html, 'test-highlighted-default-class'));
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
    public function testRenderWithNoDeclaredColumn()
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

    /**
     * @expectedException ErrorException
     * @expectedExceptionMessage  A column with no given attribute has no defined title. Please define a title for this
     *                            column using the "setTitle()" method on the column object.
     */
    public function testRenderWithColumnWithNoAttributeAndNoTitle()
    {
        $routes = [
            'index' => ['alias' => 'users.index', 'parameters' => []],
        ];
        $table = app(TableList::class)->setRoutes($routes)->setModel(User::class);
        $table->addColumn();
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

    public function testSearchInaccurateRequest()
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

    public function testToHtml()
    {
        $this->setRoutes(['users'], ['index', 'create', 'edit', 'destroy']);
        $routes = [
            'index'   => ['alias' => 'users.index', 'parameters' => []],
            'create'  => ['alias' => 'users.create', 'parameters' => []],
            'edit'    => ['alias' => 'users.edit', 'parameters' => []],
            'destroy' => ['alias' => 'users.destroy', 'parameters' => []],
        ];
        $this->createMultipleUsers(5);
        $table = app(TableList::class)->setRoutes($routes)
            ->setModel(User::class)
            ->setRoutes($routes);
        $table->addColumn('name')->setTitle('Name')->sortByDefault()->useForDestroyConfirmation();
        $table->addColumn('email')->setTitle('Email');
        $this->assertEquals($table->render(), $table->toHtml());
    }
}
