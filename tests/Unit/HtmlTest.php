<?php

namespace Okipa\LaravelBootstrapTableList\Tests\Unit;

use Okipa\LaravelBootstrapTableList\TableList;
use Okipa\LaravelBootstrapTableList\Test\Fakers\RoutesFaker;
use Okipa\LaravelBootstrapTableList\Test\Fakers\UsersFaker;
use Okipa\LaravelBootstrapTableList\Test\Models\User;
use Okipa\LaravelBootstrapTableList\Test\TableListTestCase;

class HtmlTest extends TableListTestCase
{
    use RoutesFaker;
    use UsersFaker;

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

    public function testTableTitleHtml()
    {
        $routes = [
            'index' => ['alias' => 'users.index', 'parameters' => []],
        ];
        $table = app(TableList::class)->setRoutes($routes)->setModel(User::class);
        $table->addColumn('name')->setTitle('Name')->sortByDefault();
        $table->addColumn('email')->setTitle('Email');
        $table->render();
        $thead = view('tablelist::thead', ['table' => $table])->render();
        $this->assertContains('Name', $thead);
        $this->assertContains('Email', $thead);
    }

    public function testEmptyListHtml()
    {
        $routes = [
            'index' => ['alias' => 'users.index', 'parameters' => []],
        ];
        $table = app(TableList::class)->setRoutes($routes)->setModel(User::class);
        $table->addColumn('name')->setTitle('Name')->sortByDefault();
        $table->render();
        $tbody = view('tablelist::tbody', ['table' => $table])->render();
        $this->assertContains(trans('tablelist::tablelist.tbody.empty'), $tbody);
    }

    public function testFilledListHtml()
    {
        $users = $this->createMultipleUsers(5);
        $routes = [
            'index' => ['alias' => 'users.index', 'parameters' => []],
        ];
        $table = app(TableList::class)->setRoutes($routes)->setModel(User::class);
        $table->addColumn('name')->setTitle('Name')->sortByDefault();
        $table->addColumn('email')->setTitle('Email');
        $table->render();
        $tbody = view('tablelist::tbody', ['table' => $table])->render();
        foreach ($users as $user) {
            $this->assertContains($user->name, $tbody);
            $this->assertContains($user->email, $tbody);
        }
    }
}
