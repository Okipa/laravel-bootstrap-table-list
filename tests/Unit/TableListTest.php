<?php

namespace Okipa\LaravelBootstrapTableList\Tests\Unit;

use ErrorException;
use Illuminate\Http\Request;
use InvalidArgumentException;
use Okipa\LaravelBootstrapTableList\TableList;
use Okipa\LaravelBootstrapTableList\Test\Fakers\RoutesFaker;
use Okipa\LaravelBootstrapTableList\Test\Fakers\UsersFaker;
use Okipa\LaravelBootstrapTableList\Test\Models\User;
use Okipa\LaravelBootstrapTableList\Test\TableListTestCase;
use View;

class TableListTest extends TableListTestCase
{
    use RoutesFaker;
    use UsersFaker;

    public function testSetModel()
    {
        $table = app(TableList::class)->setModel(User::class);
        $this->assertEquals(app(User::class), $table->tableModel);
    }

    public function testSetRequest()
    {
        $customRequest = app(Request::class);
        $customRequest->merge([
            'customField' => 'test',
        ]);
        $table = app(TableList::class)->setRequest($customRequest);
        $this->assertEquals($customRequest, $table->request);
    }

    public function testAddQueryInstructions()
    {
        $queryClosure = function($query) {
            $query->select('users.*')->where('users.activated');
        };
        $table = app(TableList::class)->addQueryInstructions($queryClosure);
        $this->assertEquals($queryClosure, $table->queryClosure);
    }
}
