<?php

namespace Okipa\LaravelBootstrapTableList\Tests\Unit;

use Okipa\LaravelBootstrapTableList\TableList;
use Okipa\LaravelBootstrapTableList\Test\Models\User;
use Okipa\LaravelBootstrapTableList\Test\TableListTestCase;

class EmptyStatusTest extends TableListTestCase
{
    public function testEmptyListHtml()
    {
        $routes = [
            'index' => ['alias' => 'users.index', 'parameters' => []],
        ];
        $table = app(TableList::class)->setRoutes($routes)->setModel(User::class);
        $table->addColumn('name')->sortByDefault();
        $table->render();
        $tbody = view('tablelist::tbody', ['table' => $table])->render();
        $this->assertContains(trans('tablelist::tablelist.tbody.empty'), $tbody);
    }

    public function testFilledListHtml()
    {
        $this->createMultipleUsers(5);
        $routes = [
            'index' => ['alias' => 'users.index', 'parameters' => []],
        ];
        $table = app(TableList::class)->setRoutes($routes)->setModel(User::class);
        $table->addColumn('name')->sortByDefault();
        $table->addColumn('email');
        $table->render();
        $tbody = view('tablelist::tbody', ['table' => $table])->render();
        $this->assertNotContains(trans('tablelist::tablelist.tbody.empty'), $tbody);
    }
}
