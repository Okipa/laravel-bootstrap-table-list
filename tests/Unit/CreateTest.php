<?php

namespace Okipa\LaravelBootstrapTableList\Tests\Unit;

use Okipa\LaravelBootstrapTableList\TableList;
use Okipa\LaravelBootstrapTableList\Test\Fakers\RoutesFaker;
use Okipa\LaravelBootstrapTableList\Test\Fakers\UsersFaker;
use Okipa\LaravelBootstrapTableList\Test\Models\User;
use Okipa\LaravelBootstrapTableList\Test\TableListTestCase;

class CreateTest extends TableListTestCase
{
    use RoutesFaker;
    use UsersFaker;

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
        $tfoot = view('tablelist::tfoot', ['table' => $table])->render();
        $this->assertContains('<div class="create-container', $tfoot);
        $this->assertContains('href="http://localhost/users/create"', $tfoot);
        $this->assertContains('title="Add"', $tfoot);
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
        $tfoot = view('tablelist::tfoot', ['table' => $table])->render();
        $this->assertNotContains('<div class="create-container', $tfoot);
        $this->assertNotContains('href="http://localhost/users/create"', $tfoot);
        $this->assertNotContains('title="Add"', $tfoot);
    }
}