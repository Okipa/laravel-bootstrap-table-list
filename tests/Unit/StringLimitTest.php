<?php

namespace Okipa\LaravelBootstrapTableList\Tests\Unit;

use Okipa\LaravelBootstrapTableList\TableList;
use Okipa\LaravelBootstrapTableList\Test\Models\User;
use Okipa\LaravelBootstrapTableList\Test\TableListTestCase;

class StringLimitTest extends TableListTestCase
{
    public function testSetStringLimitAttribute()
    {
        $table = app(TableList::class)->setModel(User::class);
        $table->addColumn('name')->setStringLimit(10);
        $this->assertEquals(10, $table->columns->first()->stringLimit);
    }

    public function testSetStringLimitHtml()
    {
        $this->setRoutes(['users'], ['index']);
        $routes = [
            'index' => ['alias' => 'users.index', 'parameters' => []],
        ];
        $user = $this->createUniqueUser();
        $table = app(TableList::class)->setRoutes($routes)
            ->setModel(User::class)
            ->setRoutes($routes);
        $table->addColumn('name')->setTitle('Name')->sortByDefault()->useForDestroyConfirmation();
        $table->addColumn('email')->setTitle('Email')->setStringLimit(2);
        $html = $table->render();
        $this->assertContains(str_limit($user->email, 2), $html);
    }
}
