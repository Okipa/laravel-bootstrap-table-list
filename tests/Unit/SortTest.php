<?php

namespace Okipa\LaravelBootstrapTableList\Tests\Unit;

use Illuminate\Http\Request;
use Okipa\LaravelBootstrapTableList\TableList;
use Okipa\LaravelBootstrapTableList\Test\Fakers\RoutesFaker;
use Okipa\LaravelBootstrapTableList\Test\Fakers\UsersFaker;
use Okipa\LaravelBootstrapTableList\Test\Models\User;
use Okipa\LaravelBootstrapTableList\Test\TableListTestCase;

class SortTest extends TableListTestCase
{
    use RoutesFaker;
    use UsersFaker;

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
        $thead = view('tablelist::thead', ['table' => $table])->render();
        $this->assertContains(
            '<a href="http://localhost/users/index?sortBy=name&amp;sortDir=desc&amp;rowsNumber=20"',
            $thead
        );
        $this->assertNotContains(
            '<a href="http://localhost/users/index?sortBy=email&amp;sortDir=desc&amp;rowsNumber=20"',
            $thead
        );
    }
}