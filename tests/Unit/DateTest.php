<?php

namespace Okipa\LaravelBootstrapTableList\Tests\Unit;

use Okipa\LaravelBootstrapTableList\TableList;
use Okipa\LaravelBootstrapTableList\Test\Models\User;
use Okipa\LaravelBootstrapTableList\Test\TableListTestCase;

class DateTest extends TableListTestCase
{
    public function testSetColumnDateFormatAttribute()
    {
        $table = app(TableList::class)->setModel(User::class);
        $table->addColumn('name')->setColumnDateFormat('d/m/Y H:i:s');
        $this->assertEquals('d/m/Y H:i:s', $table->columns->first()->columnDateFormat);
    }
}
