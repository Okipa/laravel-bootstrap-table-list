<?php

namespace Okipa\LaravelBootstrapTableList\Tests\Unit;

use Okipa\LaravelBootstrapTableList\TableList;
use Okipa\LaravelBootstrapTableList\Test\Fakers\RoutesFaker;
use Okipa\LaravelBootstrapTableList\Test\Fakers\UsersFaker;
use Okipa\LaravelBootstrapTableList\Test\Models\User;
use Okipa\LaravelBootstrapTableList\Test\TableListTestCase;

class EditTest extends TableListTestCase
{
    use RoutesFaker;
    use UsersFaker;

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
        $tbody = view('tablelist::tbody', ['table' => $table])->render();
        foreach ($users as $user) {
            $this->assertContains('<form class="edit-' . $user->id, $tbody);
            $this->assertContains('action="http://localhost/users/edit?id=' . $user->id . '"', $tbody);
        }
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
        $tbody = view('tablelist::tbody', ['table' => $table])->render();
        foreach ($users as $user) {
            $this->assertNotContains('<form class="edit-' . $user->id, $tbody);
            $this->assertNotContains('action="http://localhost/users/edit?id=' . $user->id . '"', $tbody);
        }
    }
}