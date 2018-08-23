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
        $closure = function($entity, $column) { };
        $table->addColumn('name')->isCustomValue($closure);
        $this->assertEquals($closure, $table->columns->first()->customValueClosure);
    }
}
