<?php

namespace Okipa\LaravelBootstrapTableList\Tests\Unit;

use Okipa\LaravelBootstrapTableList\TableList;
use Okipa\LaravelBootstrapTableList\Test\Models\User;
use Okipa\LaravelBootstrapTableList\Test\TableListTestCase;

class IconTest extends TableListTestCase
{
    public function testSetIconAttribute()
    {
        $table = app(TableList::class)->setModel(User::class);
        $table->addColumn('name')->setIcon('icon');
        $this->assertEquals('icon', $table->columns->first()->icon);
        $this->assertEquals(false, $table->columns->first()->showIconWithNoValue);
    }

    public function testSetIconAttributeAndSetShowWithNoValue()
    {
        $table = app(TableList::class)->setModel(User::class);
        $table->addColumn('name')->setIcon('icon', true);
        $this->assertEquals('icon', $table->columns->first()->icon);
        $this->assertEquals(true, $table->columns->first()->showIconWithNoValue);
    }

    public function testSetIconHtml()
    {
        $this->createMultipleUsers(1);
        $this->setRoutes(['users'], ['index']);
        $routes = [
            'index' => ['alias' => 'users.index', 'parameters' => []],
        ];
        $table = app(TableList::class)->setRoutes($routes)->setModel(User::class);
        $table->addColumn('name')->sortByDefault()->useForDestroyConfirmation()->setIcon('icon');
        $table->render();
        $html = view('tablelist::tbody', ['table' => $table])->render();
        $this->assertContains('icon', $html);
    }

    public function testSetIconWithNoValueHtml()
    {
        $user = $this->createUniqueUser();
        $user->update(['name' => null]);
        $this->setRoutes(['users'], ['index']);
        $routes = [
            'index' => ['alias' => 'users.index', 'parameters' => []],
        ];
        $table = app(TableList::class)->setRoutes($routes)->setModel(User::class);
        $table->addColumn('name')->sortByDefault()->useForDestroyConfirmation()->setIcon('icon');
        $table->render();
        $html = view('tablelist::tbody', ['table' => $table])->render();
        $this->assertNotContains('icon', $html);
    }

    public function testSetIconWithNoButShowAnywayValueHtml()
    {
        $user = $this->createUniqueUser();
        $user->update(['name' => null]);
        $this->setRoutes(['users'], ['index']);
        $routes = [
            'index' => ['alias' => 'users.index', 'parameters' => []],
        ];
        $table = app(TableList::class)->setRoutes($routes)->setModel(User::class);
        $table->addColumn('name')->sortByDefault()->useForDestroyConfirmation()->setIcon('icon', true);
        $table->render();
        $html = view('tablelist::tbody', ['table' => $table])->render();
        $this->assertContains('icon', $html);
    }
}
