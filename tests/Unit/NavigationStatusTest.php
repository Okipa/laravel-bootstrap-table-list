<?php

namespace Okipa\LaravelBootstrapTableList\Tests\Unit;

use Illuminate\Http\Request;
use Okipa\LaravelBootstrapTableList\TableList;
use Okipa\LaravelBootstrapTableList\Test\Fakers\RoutesFaker;
use Okipa\LaravelBootstrapTableList\Test\Fakers\UsersFaker;
use Okipa\LaravelBootstrapTableList\Test\Models\User;
use Okipa\LaravelBootstrapTableList\Test\TableListTestCase;

class NavigationStatusTest extends TableListTestCase
{
    use RoutesFaker;
    use UsersFaker;

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
}