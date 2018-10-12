<?php

namespace Okipa\LaravelBootstrapTableList\Tests\Unit;

use ErrorException;
use Illuminate\Http\Request;
use Okipa\LaravelBootstrapTableList\TableList;
use Okipa\LaravelBootstrapTableList\Test\Models\User;
use Okipa\LaravelBootstrapTableList\Test\TableListTestCase;

class SortTest extends TableListTestCase
{
    public function testSetIsSortableAttribute()
    {
        $table = app(TableList::class)->setModel(User::class);
        $table->addColumn('name')->isSortable();
        $this->assertTrue($table->columns->first()->isSortableColumn);
        $this->assertEquals(1, $table->sortableColumns->count());
        $this->assertEquals('name', $table->sortableColumns->first()->attribute);
    }

    public function testSetSortByDefaultAttribute()
    {
        $table = app(TableList::class)->setModel(User::class);
        $table->addColumn('name')->sortByDefault('desc');
        $this->assertEquals('name', $table->sortBy);
        $this->assertEquals('desc', $table->sortDir);
    }

    public function testSortByDefault()
    {
        $users = $this->createMultipleUsers(5);
        $this->setRoutes(['users'], ['index']);
        $routes = [
            'index' => ['alias' => 'users.index', 'parameters' => []],
        ];
        $table = app(TableList::class)->setRoutes($routes)->setModel(User::class);
        $table->addColumn('name')->setTitle('Name');
        $table->addColumn('email')->setTitle('Email')->sortByDefault();
        $table->render();
        $this->assertEquals($users->sortBy('email')->values()->toArray(), $table->list->toArray()['data']);
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
     * @expectedExceptionMessage One of the sortable columns has no defined attribute. You have to define a column
     *                           attribute for each sortable columns by setting a string parameter in the « addColumn()
     *                           » method.
     */
    public function testSortByColumnWithoutAttribute()
    {
        $this->createMultipleUsers(5);
        $this->setRoutes(['companies'], ['index']);
        $routes = [
            'index' => ['alias' => 'companies.index', 'parameters' => []],
        ];
        $table = app(TableList::class)->setRoutes($routes)->setModel(User::class);
        $table->addColumn()->isSortable();
        $table->render();
    }

    public function testSortByColumn()
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
