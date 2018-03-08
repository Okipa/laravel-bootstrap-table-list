<?php

namespace Okipa\LaravelBootstrapTableList\Tests\Unit;

use ErrorException;
use Okipa\LaravelBootstrapTableList\TableList;
use Okipa\LaravelBootstrapTableList\Test\helpers\Routes;
use Okipa\LaravelBootstrapTableList\Test\helpers\Users;
use Okipa\LaravelBootstrapTableList\Tests\Models\User;
use Tests\TableListTestCase;

class TableListColumnTest extends TableListTestCase
{
    use Routes;
    use Users;

    public function setUp()
    {
        parent::setUp();
        $this->instanciateFaker();
    }

    /**
     * @expectedException ErrorException
     * @expectedExceptionMessage  The sortByDefault() method has already been called. You can sort a column by default
     *                            only once.
     */
    public function testSortByDefaultCalledSeveralTimes()
    {
        $this->createMultipleUsers(10);
        $routes = [
            'index' => ['alias' => 'users.index', 'parameters' => []],
        ];
        $table = app(TableList::class)->setRoutes($routes)->setModel(User::class);
        $table->addColumn('name')->setTitle('Name')->sortByDefault();
        $table->addColumn('email')->setTitle('Email')->sortByDefault();
        $table->render();
    }
}
