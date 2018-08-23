<?php

namespace Okipa\LaravelBootstrapTableList\Tests\Unit;

use Okipa\LaravelBootstrapTableList\TableList;
use Okipa\LaravelBootstrapTableList\Test\Models\User;
use Okipa\LaravelBootstrapTableList\Test\TableListTestCase;

class TitleTest extends TableListTestCase
{
    public function testSetTitleAttribute()
    {
        $table = app(TableList::class)->setModel(User::class);
        $table->addColumn('name')->setTitle('Name');
        $this->assertEquals('Name', $table->columns->first()->title);
    }

    public function testSetTitleHtml()
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
}
