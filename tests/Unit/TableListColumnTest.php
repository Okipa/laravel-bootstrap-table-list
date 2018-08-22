<?php

namespace Okipa\LaravelBootstrapTableList\Test\Unit;

use Illuminate\Http\Request;
use Okipa\LaravelBootstrapTableList\TableList;
use Okipa\LaravelBootstrapTableList\Test\Fakers\RoutesFaker;
use Okipa\LaravelBootstrapTableList\Test\Fakers\UsersFaker;
use Okipa\LaravelBootstrapTableList\Test\Models\User;
use Okipa\LaravelBootstrapTableList\Test\TableListTestCase;

class TableListColumnTest extends TableListTestCase
{
    use RoutesFaker;
    use UsersFaker;

    public function testSetTitleAttribute()
    {
        $table = app(TableList::class)->setModel(User::class);
        $table->addColumn('name')->setTitle('Name');
        $this->assertEquals('Name', $table->columns->first()->title);
    }

    public function testSetStringLimitAttribute()
    {
        $table = app(TableList::class)->setModel(User::class);
        $table->addColumn('name')->setStringLimit(10);
        $this->assertEquals(10, $table->columns->first()->stringLimit);
    }

    public function testIsSearchableAttribute()
    {
        $table = app(TableList::class)->setModel(User::class);
        $table->addColumn('name')->isSearchable();
        $this->assertEquals('name', $table->searchableColumns->first()->attribute);
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
    
    public function testSortByDefaultAttribute()
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
    
    public function testUseForDestroyConfirmationSingle()
    {
        $table = app(TableList::class)->setModel(User::class);
        $table->addColumn('name')->useForDestroyConfirmation();
        $this->assertEquals(['name'], $table->destroyAttributes->toArray());
    }

    public function testUseForDestroyConfirmationMultiple()
    {
        $table = app(TableList::class)->setModel(User::class);
        $table->addColumn('name')->useForDestroyConfirmation();
        $table->addColumn('email')->useForDestroyConfirmation();
        $this->assertEquals(['name', 'email'], $table->destroyAttributes->toArray());
    }

    public function testIsSortable()
    {
        $table = app(TableList::class)->setModel(User::class);
        $table->addColumn('name')->isSortable();
        $this->assertTrue($table->columns->first()->isSortableColumn);
        $this->assertEquals(1, $table->sortableColumns->count());
        $this->assertEquals('name', $table->sortableColumns->first()->attribute);
    }

    public function testSetCustomTableAttributeOnly()
    {
        $table = app(TableList::class)->setModel(User::class);
        $table->addColumn('name')->setCustomTable('custom_table');
        $this->assertEquals('custom_table', $table->columns->first()->customColumnTable);
    }

    public function testSetCustomTableAndAliasAttributes()
    {
        $table = app(TableList::class)->setModel(User::class);
        $table->addColumn('name')->setCustomTable('custom_table', 'alias');
        $this->assertEquals('custom_table', $table->columns->first()->customColumnTable);
        $this->assertEquals('alias', $table->columns->first()->columnDatabaseAlias);
    }

    public function testSetColumnDateFormat()
    {
        $table = app(TableList::class)->setModel(User::class);
        $table->addColumn('name')->setColumnDateFormat('d/m/Y H:i:s');
        $this->assertEquals('d/m/Y H:i:s', $table->columns->first()->columnDateFormat);
    }

    public function testIsButton()
    {
        $table = app(TableList::class)->setModel(User::class);
        $table->addColumn('name')->isButton(['buttonClass']);
        $this->assertEquals(['buttonClass'], $table->columns->first()->buttonClass);
    }
    
    public function testSetIcon()
    {
        $table = app(TableList::class)->setModel(User::class);
        $table->addColumn('name')->setIcon('icon');
        $this->assertEquals('icon', $table->columns->first()->icon);
    }

    public function testIsLinkEmpty()
    {
        $table = app(TableList::class)->setModel(User::class);
        $table->addColumn('name')->isLink();
        $this->assertEquals(true, $table->columns->first()->url);
    }
    
    public function testIsLinkString()
    {
        $table = app(TableList::class)->setModel(User::class);
        $table->addColumn('name')->isLink('link');
        $this->assertEquals('link', $table->columns->first()->url);
    }
    
    public function testIsLinkClosure()
    {
        $table = app(TableList::class)->setModel(User::class);
        $closure = function($entity, $column) {};
        $table->addColumn('name')->isLink($closure);
        $this->assertEquals($closure, $table->columns->first()->url);
    }

    public function testIsCustomValue()
    {
        $table = app(TableList::class)->setModel(User::class);
        $closure = function($entity, $column) { };
        $table->addColumn('name')->isCustomValue($closure);
        $this->assertEquals($closure, $table->columns->first()->customValueClosure);
    }

    public function testIsCustomHtmlElement()
    {
        $table = app(TableList::class)->setModel(User::class);
        $closure = function($entity, $column) { };
        $table->addColumn('name')->isCustomHtmlElement($closure);
        $this->assertEquals($closure, $table->columns->first()->customHtmlEltClosure);
    }

    public function testSetRowsNumberAttribute()
    {
        $rowsNumber = 10;
        $table = app(TableList::class)->setRowsNumber($rowsNumber);
        $this->assertEquals($rowsNumber, $table->rowsNumber);
    }
}
