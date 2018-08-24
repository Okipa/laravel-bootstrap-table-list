<?php

namespace Okipa\LaravelBootstrapTableList\Tests\Unit;

use Okipa\LaravelBootstrapTableList\TableList;
use Okipa\LaravelBootstrapTableList\Test\Models\User;
use Okipa\LaravelBootstrapTableList\Test\TableListTestCase;

class ButtonTest extends TableListTestCase
{
    public function testSetIsButtonAttribute()
    {
        $table = app(TableList::class)->setModel(User::class);
        $table->addColumn('name')->isButton(['buttonClass']);
        $this->assertEquals(['buttonClass'], $table->columns->first()->buttonClass);
    }

    public function testIsButtonHtml()
    {
        $this->createUniqueUser();
        $this->setRoutes(['users'], ['index']);
        $routes = [
            'index' => ['alias' => 'users.index', 'parameters' => []],
        ];
        $table = app(TableList::class)->setRoutes($routes)->setModel(User::class);
        $table->addColumn('name')->sortByDefault()->useForDestroyConfirmation()->isButton(['btn', 'btn-primary']);
        $table->render();
        $html = view('tablelist::tbody', ['table' => $table])->render();
        $this->assertContains('<button class="btn btn-primary', $html);
    }

    public function testIsButtonWithNoValueHtml()
    {
        $user = $this->createUniqueUser();
        $user->update(['name' => null]);
        $this->setRoutes(['users'], ['index']);
        $routes = [
            'index' => ['alias' => 'users.index', 'parameters' => []],
        ];
        $table = app(TableList::class)->setRoutes($routes)->setModel(User::class);
        $table->addColumn('name')->sortByDefault()->useForDestroyConfirmation()->isButton(['btn', 'btn-primary']);
        $table->render();
        $html = view('tablelist::tbody', ['table' => $table])->render();
        $this->assertNotContains('<button class="btn btn-primary', $html);
    }

    public function testIsButtonWithNoValueWithIconHtml()
    {
        $user = $this->createUniqueUser();
        $user->update(['name' => null]);
        $this->setRoutes(['users'], ['index']);
        $routes = [
            'index' => ['alias' => 'users.index', 'parameters' => []],
        ];
        $table = app(TableList::class)->setRoutes($routes)->setModel(User::class);
        $table->addColumn('name')
            ->sortByDefault()
            ->useForDestroyConfirmation()
            ->isButton(['btn', 'btn-primary'])
            ->setIcon('icon', true);
        $table->render();
        $html = view('tablelist::tbody', ['table' => $table])->render();
        $this->assertContains('<button class="btn btn-primary', $html);
    }
}
