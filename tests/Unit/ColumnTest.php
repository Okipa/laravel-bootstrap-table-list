<?php

namespace Okipa\LaravelBootstrapTableList\Tests\Unit;

use Okipa\LaravelBootstrapTableList\TableList;
use Okipa\LaravelBootstrapTableList\Test\Fakers\RoutesFaker;
use Okipa\LaravelBootstrapTableList\Test\Fakers\UsersFaker;
use Okipa\LaravelBootstrapTableList\Test\Models\User;
use Okipa\LaravelBootstrapTableList\Test\TableListTestCase;

class ColumnTest extends TableListTestCase
{
    use RoutesFaker;
    use UsersFaker;

    /**
     * @expectedException ErrorException
     * @expectedExceptionMessage  The table list model has not been defined or is not an instance of
     *                            Illuminate\Database\Eloquent\Model.
     */
    public function testAddColumnWithoutModel()
    {
        app(TableList::class)->addColumn('name');
    }

    public function testAddColumn()
    {
        $columnAttribute = 'name';
        $table = app(TableList::class)->setModel(User::class);
        $table->addColumn($columnAttribute);
        $this->assertEquals($table->columns->count(), 1);
        $this->assertEquals($table->columns->first()->tableList, $table);
        $this->assertEquals($table->columns->first()->customColumnTable, app(User::class)->getTable());
        $this->assertEquals($table->columns->first()->attribute, $columnAttribute);
    }

    public function testGetColumnsCount()
    {
        $table = app(TableList::class)->setModel(User::class);
        $table->addColumn('id');
        $table->addColumn('name');
        $table->addColumn('email');
        $this->assertEquals(3, $table->getColumnsCount());
    }

    /**
     * @expectedException ErrorException
     * @expectedExceptionMessage  No column has been added to the table list. Please add at least one column by using
     *                            the "addColumn" method on the table list object.
     */
    public function testRenderWithNoDeclaredColumn()
    {
        $routes = [
            'index' => ['alias' => 'users.index', 'parameters' => []],
        ];
        $table = app(TableList::class)->setRoutes($routes)->setModel(User::class);
        $table->render();
    }

    /**
     * @expectedException ErrorException
     * @expectedExceptionMessage  The given column attribute "not_existing_column" does not exist in the "users_test"
     *                            table.
     */
    public function testRenderWithNotExistingColumn()
    {
        $routes = [
            'index' => ['alias' => 'users.index', 'parameters' => []],
        ];
        $table = app(TableList::class)->setRoutes($routes)->setModel(User::class);
        $table->addColumn('not_existing_column');
        $table->render();
    }

    public function testRenderWithColumnWithAttributeAndNoTitle()
    {
        $this->setRoutes(['users'], ['index']);
        $routes = [
            'index' => ['alias' => 'users.index', 'parameters' => []],
        ];
        $table = app(TableList::class)->setRoutes($routes)->setModel(User::class);
        $table->addColumn('name')->sortByDefault()->isSearchable();
        $html = $table->render();
        $this->assertContains('validation.attributes.name', $html);
    }

    /**
     * @expectedException ErrorException
     * @expectedExceptionMessage  A column with no given attribute has no defined title. Please define a title for this
     *                            column using the "setTitle()" method on the column object.
     */
    public function testRenderWithColumnWithNoAttributeAndNoTitle()
    {
        $routes = [
            'index' => ['alias' => 'users.index', 'parameters' => []],
        ];
        $table = app(TableList::class)->setRoutes($routes)->setModel(User::class);
        $table->addColumn();
        $table->render();
    }
}