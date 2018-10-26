<?php

namespace Okipa\LaravelBootstrapTableList\Tests\Unit;

use Okipa\LaravelBootstrapTableList\TableList;
use Okipa\LaravelBootstrapTableList\Test\Models\User;
use Okipa\LaravelBootstrapTableList\Test\TableListTestCase;

class CustomHtmlElementTest extends TableListTestCase
{
    public function testSetIsCustomHtmlElementAttribute()
    {
        $table = app(TableList::class)->setModel(User::class);
        $closure = function ($entity, $column) {
        };
        $table->addColumn('name')->isCustomHtmlElement($closure);
        $this->assertEquals($closure, $table->columns->first()->customHtmlEltClosure);
    }
}
