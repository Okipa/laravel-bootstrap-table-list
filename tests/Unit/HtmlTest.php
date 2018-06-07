<?php

namespace Okipa\LaravelBootstrapTableList\Tests\Unit;

use Okipa\LaravelBootstrapTableList\TableList;
use Okipa\LaravelBootstrapTableList\Test\Fakers\RoutesFaker;
use Okipa\LaravelBootstrapTableList\Test\Fakers\UsersFaker;
use Okipa\LaravelBootstrapTableList\Test\Models\User;
use Okipa\LaravelBootstrapTableList\Test\TableListTestCase;

class HtmlTest extends TableListTestCase
{
    use RoutesFaker;
    use UsersFaker;

    public function testTableTitleHtml()
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

    public function testNavigationStatusHtml()
    {
        $routes = [
            'index' => ['alias' => 'users.index', 'parameters' => []],
        ];
        $table = app(TableList::class)->setRoutes($routes)->setModel(User::class);
        $table->addColumn('name')->setTitle('Name')->sortByDefault();
        $table->render();
        $tfoot = view('tablelist::tfoot', ['table' => $table])->render();
        $this->assertContains($table->navigationStatus(), $tfoot);
    }

    public function testEmptyListHtml()
    {
        $routes = [
            'index' => ['alias' => 'users.index', 'parameters' => []],
        ];
        $table = app(TableList::class)->setRoutes($routes)->setModel(User::class);
        $table->addColumn('name')->setTitle('Name')->sortByDefault();
        $table->render();
        $tbody = view('tablelist::tbody', ['table' => $table])->render();
        $this->assertContains(trans('tablelist::tablelist.tbody.empty'), $tbody);
    }

    public function testFilledListHtml()
    {
        $users = $this->createMultipleUsers(5);
        $routes = [
            'index' => ['alias' => 'users.index', 'parameters' => []],
        ];
        $table = app(TableList::class)->setRoutes($routes)->setModel(User::class);
        $table->addColumn('name')->setTitle('Name')->sortByDefault();
        $table->addColumn('email')->setTitle('Email');
        $table->render();
        $tbody = view('tablelist::tbody', ['table' => $table])->render();
        foreach ($users as $user) {
            $this->assertContains($user->name, $tbody);
            $this->assertContains($user->email, $tbody);
        }
    }

    public function testNoCreateActionHtml()
    {
        $this->setRoutes(['users'], ['create']);
        $routes = [
            'index' => ['alias' => 'users.index', 'parameters' => []],
        ];
        $table = app(TableList::class)->setRoutes($routes)->setModel(User::class);
        $table->addColumn('name')->setTitle('Name')->sortByDefault();
        $table->render();
        // tfoot
        $tfoot = view('tablelist::tfoot', ['table' => $table])->render();
        $this->assertNotContains('<div class="tfoot-tab col-sm-4 create-button">', $tfoot);
        $this->assertNotContains('href="http://localhost/users/create"', $tfoot);
        $this->assertNotContains('title="Add"', $tfoot);
    }

    public function testCreateActionHtml()
    {
        $this->setRoutes(['users'], ['create']);
        $routes = [
            'index'  => ['alias' => 'users.index', 'parameters' => []],
            'create' => ['alias' => 'users.create', 'parameters' => []],
        ];
        $table = app(TableList::class)->setRoutes($routes)->setModel(User::class);
        $table->addColumn('name')->setTitle('Name')->sortByDefault();
        $table->render();
        $tfoot = view('tablelist::tfoot', ['table' => $table])->render();
        $this->assertContains('<div class="tfoot-tab col-sm-4 create-button">', $tfoot);
        $this->assertContains('href="http://localhost/users/create"', $tfoot);
        $this->assertContains('title="Add"', $tfoot);
    }

    public function testNoEditActionHtml()
    {
        $users = $this->createMultipleUsers(5);
        $routes = [
            'index' => ['alias' => 'users.index', 'parameters' => []],
        ];
        $table = app(TableList::class)->setRoutes($routes)->setModel(User::class);
        $table->addColumn('name')->setTitle('Name')->sortByDefault();
        $table->render();
        $tbody = view('tablelist::tbody', ['table' => $table])->render();
        foreach ($users as $user) {
            $this->assertNotContains('action="http://localhost/users/edit?id=' . $user->id . '"', $tbody);
        }
    }

    public function testEditActionHtml()
    {
        $users = $this->createMultipleUsers(5);
        $this->setRoutes(['users'], ['edit']);
        $routes = [
            'index' => ['alias' => 'users.index', 'parameters' => []],
            'edit'  => ['alias' => 'users.edit', 'parameters' => []],
        ];
        $table = app(TableList::class)->setRoutes($routes)->setModel(User::class);
        $table->addColumn('name')->setTitle('Name')->sortByDefault();
        $table->render();
        $tbody = view('tablelist::tbody', ['table' => $table])->render();
        foreach ($users as $user) {
            $this->assertContains('action="http://localhost/users/edit?id=' . $user->id . '"', $tbody);
        }
    }

    public function testNoDestroyActionHtml()
    {
        $users = $this->createMultipleUsers(5);
        $routes = [
            'index' => ['alias' => 'users.index', 'parameters' => []],
        ];
        $table = app(TableList::class)->setRoutes($routes)->setModel(User::class);
        $table->addColumn('name')->setTitle('Name')->sortByDefault()->useForDestroyConfirmation();
        $table->render();
        $tbody = view('tablelist::tbody', ['table' => $table])->render();
        foreach ($users as $user) {
            $this->assertNotContains('action="http://localhost/users/destroy?id=' . $user->id . '"', $tbody);
            $this->assertNotContains(trans('tablelist::tablelist.modal.question', [
                'entity' => $user->{$table->destroyAttribute},
            ]), $tbody);
        }
    }

    public function testDestroyActionHtml()
    {
        $users = $this->createMultipleUsers(5);
        $this->setRoutes(['users'], ['destroy']);
        $routes = [
            'index'   => ['alias' => 'users.index', 'parameters' => []],
            'destroy' => ['alias' => 'users.destroy', 'parameters' => []],
        ];
        $table = app(TableList::class)->setRoutes($routes)->setModel(User::class);
        $table->addColumn('name')->setTitle('Name')->sortByDefault()->useForDestroyConfirmation();
        $table->render();
        $tbody = view('tablelist::tbody', ['table' => $table])->render();
        foreach ($users as $user) {
            $this->assertContains('action="http://localhost/users/destroy?id=' . $user->id . '"', $tbody);
            $this->assertContains(trans('tablelist::tablelist.modal.question', [
                'entity' => $user->{$table->destroyAttribute},
            ]), $tbody);
        }
    }

    public function testNoSearchableHtml()
    {
        $this->setRoutes(['users'], ['index']);
        $routes = ['index' => ['alias' => 'users.index', 'parameters' => []]];
        $table = app(TableList::class)->setRoutes($routes)->setModel(User::class);
        $table->addColumn('name')->setTitle('Name')->sortByDefault('desc');
        $table->addColumn('email')->setTitle('Email');
        $table->render();
        $thead = view('tablelist::thead', ['table' => $table])->render();
        $this->assertNotContains('div class="col-sm-6 col-xs-12 search-bar">', $thead);
        $this->assertNotContains('<form role="form" method="GET" action="http://localhost/users/index">', $thead);
        $this->assertNotContains('<input type="hidden" name="rowsNumber" value="20">', $thead);
        $this->assertNotContains('<input type="hidden" name="sortBy" value="name">', $thead);
        $this->assertNotContains('<input type="hidden" name="sortDir" value="desc">', $thead);
        $this->assertNotContains('name="search"', $thead);
        $this->assertNotContains(
            'placeholder="' . trans('tablelist::tablelist.thead.search') . ' ' . $table->getSearchableTitles() . '"',
            $thead
        );
        $this->assertNotContains(
            'title="' . trans('tablelist::tablelist.thead.search') . ' ' . $table->getSearchableTitles() . '"',
            $thead
        );
    }

    public function testSearchableHtml()
    {
        $this->setRoutes(['users'], ['index']);
        $routes = ['index' => ['alias' => 'users.index', 'parameters' => []]];
        $table = app(TableList::class)->setRoutes($routes)->setModel(User::class);
        $table->addColumn('name')->setTitle('Name')->sortByDefault('desc');
        $table->addColumn('email')->setTitle('Email')->isSearchable();
        $table->render();
        $thead = view('tablelist::thead', ['table' => $table])->render();
        $this->assertContains('div class="col-sm-6 col-xs-12 search-bar">', $thead);
        $this->assertContains('<form role="form" method="GET" action="http://localhost/users/index">', $thead);
        $this->assertContains('<input type="hidden" name="rowsNumber" value="20">', $thead);
        $this->assertContains('<input type="hidden" name="sortBy" value="name">', $thead);
        $this->assertContains('<input type="hidden" name="sortDir" value="desc">', $thead);
        $this->assertContains('name="search"', $thead);
        $this->assertContains(
            'placeholder="' . trans('tablelist::tablelist.thead.search') . ' '
            . $table->getSearchableTitles() . '"', $thead
        );
        $this->assertContains(
            'title="' . trans('tablelist::tablelist.thead.search') . ' '
            . $table->getSearchableTitles() . '"', $thead
        );
    }

    public function testRowsNumberSelectionHtml()
    {
        $this->setRoutes(['users'], ['index']);
        $routes = [
            'index' => ['alias' => 'users.index', 'parameters' => []],
        ];
        $table = app(TableList::class)->setRoutes($routes)->setModel(User::class)->enableRowsNumberSelector();
        $table->addColumn('name')->setTitle('Name')->sortByDefault();
        $table->addColumn('email')->setTitle('Email');
        $table->render();
        $thead = view('tablelist::thead', ['table' => $table])->render();
        $this->assertContains('<div class="col-sm-4 col-xs-12 rows-number-selector">', $thead);
        $this->assertContains('<form role="form" method="GET" action="http://localhost/users/index">', $thead);
        $this->assertContains('<input type="hidden" name="search" value="">', $thead);
        $this->assertContains('<input type="hidden" name="sortBy" value="name">', $thead);
        $this->assertContains('<input type="hidden" name="sortDir" value="asc">', $thead);
        $this->assertContains('type="number"', $thead);
        $this->assertContains('name="rowsNumber"', $thead);
        $this->assertContains('value="20"', $thead);
        $this->assertContains('placeholder="' . trans('tablelist::tablelist.thead.rows_number') . '"', $thead);
        $this->assertContains('title="' . trans('tablelist::tablelist.thead.rows_number') . '"', $thead);
    }

    public function testRowsNumberCustomHtml()
    {
        $this->setRoutes(['users'], ['index']);
        $routes = [
            'index' => ['alias' => 'users.index', 'parameters' => []],
        ];
        $table = app(TableList::class)
            ->setRoutes($routes)
            ->setModel(User::class)
            ->enableRowsNumberSelector()
            ->setRowsNumber(15);
        $table->addColumn('name')->setTitle('Name')->sortByDefault();
        $table->addColumn('email')->setTitle('Email');
        $table->render();
        $thead = view('tablelist::thead', ['table' => $table])->render();
        $this->assertContains('<div class="col-sm-4 col-xs-12 rows-number-selector">', $thead);
        $this->assertContains('<form role="form" method="GET" action="http://localhost/users/index">', $thead);
        $this->assertContains('<input type="hidden" name="search" value="">', $thead);
        $this->assertContains('<input type="hidden" name="sortBy" value="name">', $thead);
        $this->assertContains('<input type="hidden" name="sortDir" value="asc">', $thead);
        $this->assertContains('type="number"', $thead);
        $this->assertContains('name="rowsNumber"', $thead);
        $this->assertContains('value="15"', $thead);
        $this->assertContains('placeholder="' . trans('tablelist::tablelist.thead.rows_number') . '"', $thead);
        $this->assertContains('title="' . trans('tablelist::tablelist.thead.rows_number') . '"', $thead);
    }

    public function testSortableColumnHtml()
    {
        $this->setRoutes(['users'], ['index']);
        $routes = [
            'index' => ['alias' => 'users.index', 'parameters' => []],
        ];
        $table = app(TableList::class)->setRoutes($routes)->setModel(User::class);
        $table->addColumn('name')->setTitle('Name')->sortByDefault()->isSortable();
        $table->addColumn('email')->setTitle('Email');
        $table->render();
        $thead = view('tablelist::thead', ['table' => $table])->render();
        $this->assertContains(
            '<a href="http://localhost/users/index?sortBy=name&amp;sortDir=desc&amp;rowsNumber=20"',
            $thead
        );
        $this->assertNotContains(
            '<a href="http://localhost/users/index?sortBy=email&amp;sortDir=desc&amp;rowsNumber=20"',
            $thead
        );
    }
}
