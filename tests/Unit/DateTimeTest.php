<?php

namespace Okipa\LaravelBootstrapTableList\Tests\Unit;

use Carbon\Carbon;
use Okipa\LaravelBootstrapTableList\TableList;
use Okipa\LaravelBootstrapTableList\Test\Models\User;
use Okipa\LaravelBootstrapTableList\Test\TableListTestCase;

class DateTimeTest extends TableListTestCase
{
    public function testSetColumnDateFormatAttribute()
    {
        $table = app(TableList::class)->setModel(User::class);
        $table->addColumn('name')->setColumnDateFormat('d/m/Y H:i:s');
        $this->assertEquals('d/m/Y H:i:s', $table->columns->first()->columnDateFormat);
    }

    public function testSetColumnDateFormatHtml()
    {
        $routes = [
            'index' => ['alias' => 'users.index', 'parameters' => []],
        ];
        $user = $this->createUniqueUser();
        $table = app(TableList::class)->setRoutes($routes)->setModel(User::class);
        $table->addColumn('name')->sortByDefault();
        $table->addColumn('updated_at')->setColumnDateFormat('d/m/Y');
        $table->render();
        $html = view('tablelist::tbody', ['table' => $table])->render();
        $this->assertContains(Carbon::parse($user->updated_at)->format('d/m/Y'), $html);
    }

    public function testSetColumnDateTimeFormatAttribute()
    {
        $table = app(TableList::class)->setModel(User::class);
        $table->addColumn('name')->setColumnDateTimeFormat('H:i');
        $this->assertEquals('H:i', $table->columns->first()->columnDateTimeFormat);
    }

    public function testSetColumnDateTimeToDateFormatHtml()
    {
        $routes = [
            'index' => ['alias' => 'users.index', 'parameters' => []],
        ];
        $user = $this->createUniqueUser();
        $table = app(TableList::class)->setRoutes($routes)->setModel(User::class);
        $table->addColumn('name')->sortByDefault();
        $table->addColumn('updated_at')->setColumnDateTimeFormat('d/m');
        $table->render();
        $html = view('tablelist::tbody', ['table' => $table])->render();
        $this->assertContains(Carbon::parse($user->updated_at)->format('d/m'), $html);
    }

    public function testSetColumnDateTimeToTimeFormatHtml()
    {
        $routes = [
            'index' => ['alias' => 'users.index', 'parameters' => []],
        ];
        $user = $this->createUniqueUser();
        $table = app(TableList::class)->setRoutes($routes)->setModel(User::class);
        $table->addColumn('name')->sortByDefault();
        $table->addColumn('updated_at')->setColumnDateTimeFormat('H\h i\m\i\n');
        $table->render();
        $html = view('tablelist::tbody', ['table' => $table])->render();
        $this->assertContains(Carbon::parse($user->updated_at)->format('H\h i\m\i\n'), $html);
    }

    public function testSetColumnDateTimeToDateTimeFormatHtml()
    {
        $routes = [
            'index' => ['alias' => 'users.index', 'parameters' => []],
        ];
        $user = $this->createUniqueUser();
        $table = app(TableList::class)->setRoutes($routes)->setModel(User::class);
        $table->addColumn('name')->sortByDefault();
        $table->addColumn('updated_at')->setColumnDateTimeFormat('d/m/Y H:i');
        $table->render();
        $html = view('tablelist::tbody', ['table' => $table])->render();
        $this->assertContains(Carbon::parse($user->updated_at)->format('d/m/Y H:i'), $html);
    }
}
