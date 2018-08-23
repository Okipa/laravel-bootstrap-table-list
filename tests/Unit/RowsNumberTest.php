<?php

namespace Okipa\LaravelBootstrapTableList\Tests\Unit;

use Illuminate\Http\Request;
use Okipa\LaravelBootstrapTableList\TableList;
use Okipa\LaravelBootstrapTableList\Test\Models\User;
use Okipa\LaravelBootstrapTableList\Test\TableListTestCase;

class RowsNumberTest extends TableListTestCase
{
    public function testSetEnableRowsNumberSelectorAttribute()
    {
        $table = app(TableList::class)->enableRowsNumberSelector();
        $this->assertTrue($table->rowsNumberSelectorEnabled);
    }

    public function testSetRowsNumberAttribute()
    {
        $rowsNumber = 10;
        $table = app(TableList::class)->setRowsNumber($rowsNumber);
        $this->assertEquals($rowsNumber, $table->rowsNumber);
    }

    public function testEnableRowsNumberSelectorHtml()
    {
        $this->setRoutes(['users'], ['index']);
        $routes = [
            'index' => ['alias' => 'users.index', 'parameters' => []],
        ];
        $table = app(TableList::class)->setRoutes($routes)->setModel(User::class)->enableRowsNumberSelector();
        $table->addColumn('name')->setTitle('Name')->sortByDefault();
        $table->addColumn('email')->setTitle('Email');
        $table->render();
        $thead = view('tablelist::thead', ['table' => $table])->render();
        $this->assertContains('<div class="rows-number-selector', $thead);
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

    public function testSetCustomRowsNumberHtml()
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
        $thead = view('tablelist::thead', ['table' => $table])->render();
        $this->assertContains('<div class="rows-number-selector', $thead);
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

    public function testSetCustomRowsNumberFromRequest()
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
}
