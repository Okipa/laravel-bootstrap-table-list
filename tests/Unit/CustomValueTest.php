<?php

namespace Okipa\LaravelBootstrapTableList\Tests\Unit;

use Okipa\LaravelBootstrapTableList\TableList;
use Okipa\LaravelBootstrapTableList\Test\Models\User;
use Okipa\LaravelBootstrapTableList\Test\TableListTestCase;

class CustomValueTest extends TableListTestCase
{
    public function testSetIsCustomValueAttribute()
    {
        $table = app(TableList::class)->setModel(User::class);
        $closure = function ($entity, $column) {};
        $table->addColumn('name')->isCustomValue($closure);
        $this->assertEquals($closure, $table->columns->first()->customValueClosure);
    }

    public function testIsCustomValueHtml()
    {
        $routes = [
            'index' => ['alias' => 'users.index', 'parameters' => []],
        ];
        $user = $this->createUniqueUser();
        $table = app(TableList::class)->setRoutes($routes)->setModel(User::class);
        $table->addColumn('name')->sortByDefault();
        $table->addColumn('updated_at')->isCustomValue(function($entity){
            return 'user name = ' . $entity->name;
        });
        $table->render();
        $html = view('tablelist::tbody', ['table' => $table])->render();
        $this->assertContains('user name = ' . $user->name, $html);
    }
}
