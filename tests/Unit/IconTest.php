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
}
