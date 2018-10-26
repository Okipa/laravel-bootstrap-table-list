<?php

namespace Okipa\LaravelBootstrapTableList\Tests\Unit;

use Illuminate\Http\Request;
use Okipa\LaravelBootstrapTableList\TableList;
use Okipa\LaravelBootstrapTableList\Test\Models\User;
use Okipa\LaravelBootstrapTableList\Test\TableListTestCase;

class SearchTest extends TableListTestCase
{
    public function testSetIsSearchableAttribute()
    {
        $table = app(TableList::class)->setModel(User::class);
        $table->addColumn('name')->isSearchable();
        $this->assertEquals('name', $table->searchableColumns->first()->attribute);
    }

    /**
     * @expectedException ErrorException
     * @expectedExceptionMessage The given searchable column attribute « not_existing_column » does not exist in the «
     *                            users_test » table. Set the correct column-related table and alias with the «
     *                            setCustomTable() » method.
     */
    public function testNotExistingSearchableColumn()
    {
        $routes = [
            'index' => ['alias' => 'users.index', 'parameters' => []],
        ];
        $table = app(TableList::class)->setRoutes($routes)->setModel(User::class);
        $table->addColumn('not_existing_column')->isSearchable();
        $table->render();
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

    public function testSearchableHtml()
    {
        $this->setRoutes(['users'], ['index']);
        $routes = ['index' => ['alias' => 'users.index', 'parameters' => []]];
        $table = app(TableList::class)->setRoutes($routes)->setModel(User::class);
        $table->addColumn('name')->setTitle('Name')->sortByDefault('desc');
        $table->addColumn('email')->setTitle('Email')->isSearchable();
        $table->render();
        $thead = view('tablelist::thead', ['table' => $table])->render();
        $this->assertContains('<div class="search-bar', $thead);
        $this->assertContains('<form role="form" method="GET" action="http://localhost/users/index">', $thead);
        $this->assertContains('<input type="hidden" name="rowsNumber" value="20">', $thead);
        $this->assertContains('<input type="hidden" name="sortBy" value="name">', $thead);
        $this->assertContains('<input type="hidden" name="sortDir" value="desc">', $thead);
        $this->assertContains('name="search"', $thead);
        $this->assertContains(
            'placeholder="' . trans('tablelist::tablelist.thead.search') . ' '
            . $table->getSearchableTitles() . '"',
            $thead
        );
        $this->assertContains(
            'title="' . trans('tablelist::tablelist.thead.search') . ' '
            . $table->getSearchableTitles() . '"',
            $thead
        );
    }

    public function testNoSearchableHtml()
    {
        $this->setRoutes(['users'], ['index']);
        $routes = ['index' => ['alias' => 'users.index', 'parameters' => []]];
        $table = app(TableList::class)->setRoutes($routes)->setModel(User::class);
        $table->addColumn('name')->setTitle('Name')->sortByDefault('desc');
        $table->addColumn('email')->setTitle('Email');
        $table->render();
        $thead = view('tablelist::thead', ['table' => $table])->render();
        $this->assertNotContains('<div class="search-bar', $thead);
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
}
