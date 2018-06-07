<?php

namespace Okipa\LaravelBootstrapTableList\Test\Unit;

use ErrorException;
use Okipa\LaravelBootstrapTableList\TableList;
use Okipa\LaravelBootstrapTableList\Test\Fakers\RoutesFaker;
use Okipa\LaravelBootstrapTableList\Test\Fakers\UsersFaker;
use Okipa\LaravelBootstrapTableList\Test\Models\User;
use Okipa\LaravelBootstrapTableList\Test\TableListTestCase;

class TableListColumnTest extends TableListTestCase
{
    use RoutesFaker;
    use UsersFaker;

    public function testSetTitle()
    {
        $table = app(TableList::class)->setModel(User::class);
        $table->addColumn('name')->setTitle('Name');
        $this->assertEquals('Name', $table->columns->first()->title);
    }

    public function testSortByDefault()
    {
        $table = app(TableList::class)->setModel(User::class);
        $table->addColumn('name')->sortByDefault('desc');
        $this->assertEquals('name', $table->sortBy);
        $this->assertEquals('desc', $table->sortDir);
    }

    /**
     * @expectedException ErrorException
     * @expectedExceptionMessage  The sortByDefault() method has already been called. You can sort a column by default
     *                            only once.
     */
    public function testSortByDefaultCalledMultiple()
    {
        $table = app(TableList::class)->setModel(User::class);
        $table->addColumn('name')->sortByDefault();
        $table->addColumn('email')->sortByDefault();
    }

    public function testUseForDestroyConfirmation()
    {
        $table = app(TableList::class)->setModel(User::class);
        $table->addColumn('name')->useForDestroyConfirmation();
        $this->assertEquals('name', $table->destroyAttribute);
    }

    /**
     * @expectedException ErrorException
     * @expectedExceptionMessage  The useForDestroyConfirmation() method has already been called. You can define a
     *                            column attribute for the destroy confirmation only once.
     */
    public function testUseForDestroyConfirmationMultiple()
    {
        $table = app(TableList::class)->setModel(User::class);
        $table->addColumn('name')->useForDestroyConfirmation();
        $table->addColumn('email')->useForDestroyConfirmation();
    }

    public function testIsSortable()
    {
        $table = app(TableList::class)->setModel(User::class);
        $table->addColumn('name')->isSortable();
        $this->assertTrue($table->columns->first()->isSortableColumn);
        $this->assertEquals(1, $table->sortableColumns->count());
        $this->assertEquals('name', $table->sortableColumns->first()->attribute);
    }

    public function testIsSearchable()
    {
        $table = app(TableList::class)->setModel(User::class);
        $table->addColumn('name')->isSearchable();
        $this->assertEquals('name', $table->searchableColumns->first()->attribute);
    }

    public function testSetCustomTable()
    {
        $table = app(TableList::class)->setModel(User::class);
        $table->addColumn('name')->setCustomTable('custom_table');
        $this->assertEquals('custom_table', $table->columns->first()->customColumnTable);
    }

    public function testSetColumnDateFormat()
    {
        $table = app(TableList::class)->setModel(User::class);
        $table->addColumn('name')->setColumnDateFormat('d/m/Y H:i:s');
        $this->assertEquals('d/m/Y H:i:s', $table->columns->first()->columnDateFormat);
    }

    public function testIsButton()
    {
        $table = app(TableList::class)->setModel(User::class);
        $table->addColumn('name')->isButton('buttonClass');
        $this->assertEquals('buttonClass', $table->columns->first()->buttonClass);
    }

    public function testSetStringLimit()
    {
        $table = app(TableList::class)->setModel(User::class);
        $table->addColumn('name')->setStringLimit(10);
        $this->assertEquals(10, $table->columns->first()->stringLimit);
    }

    public function testIsLink()
    {
        $table = app(TableList::class)->setModel(User::class);
        $closure = function($entity, $column) { };
        $table->addColumn('name')->isLink($closure);
        $this->assertEquals($closure, $table->columns->first()->linkClosure);
    }

    public function testIsCustomValue()
    {
        $table = app(TableList::class)->setModel(User::class);
        $closure = function($entity, $column) { };
        $table->addColumn('name')->isCustomValue($closure);
        $this->assertEquals($closure, $table->columns->first()->customValueClosure);
    }

    public function testIsCustomHtmlElement()
    {
        $table = app(TableList::class)->setModel(User::class);
        $closure = function($entity, $column) { };
        $table->addColumn('name')->isCustomHtmlElement($closure);
        $this->assertEquals($closure, $table->columns->first()->customHtmlEltClosure);
    }
}
