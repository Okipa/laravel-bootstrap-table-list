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

    public function testToHtml()
    {
        $this->setRoutes(['users'], ['index', 'create', 'edit', 'destroy']);
        $routes = [
            'index'   => ['alias' => 'users.index', 'parameters' => []],
            'create'  => ['alias' => 'users.create', 'parameters' => []],
            'edit'    => ['alias' => 'users.edit', 'parameters' => []],
            'destroy' => ['alias' => 'users.destroy', 'parameters' => []],
        ];
        $this->createMultipleUsers(5);
        $table = app(TableList::class)->setRoutes($routes)
            ->setModel(User::class)
            ->setRoutes($routes);
        $table->addColumn('name')->setTitle('Name')->sortByDefault()->useForDestroyConfirmation();
        $table->addColumn('email')->setTitle('Email');
        $this->assertEquals($table->render(), $table->toHtml());
    }

    public function testEmptyListHtml()
    {
        $routes = [
            'index' => ['alias' => 'users.index', 'parameters' => []],
        ];
        $table = app(TableList::class)->setRoutes($routes)->setModel(User::class);
        $table->addColumn('name')->sortByDefault();
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
        $table->addColumn('name')->sortByDefault();
        $table->addColumn('email');
        $table->render();
        $tbody = view('tablelist::tbody', ['table' => $table])->render();
        foreach ($users as $user) {
            $this->assertContains($user->name, $tbody);
            $this->assertContains($user->email, $tbody);
        }
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
        $this->assertContains('<div class="rows-number-selector', $thead);
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
        $this->assertContains('<div class="rows-number-selector', $thead);
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
        $this->assertContains('<div class="create-container', $tfoot);
        $this->assertContains('href="http://localhost/users/create"', $tfoot);
        $this->assertContains('title="Add"', $tfoot);
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
        $tfoot = view('tablelist::tfoot', ['table' => $table])->render();
        $this->assertNotContains('<div class="create-container', $tfoot);
        $this->assertNotContains('href="http://localhost/users/create"', $tfoot);
        $this->assertNotContains('title="Add"', $tfoot);
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
            $this->assertContains('<form class="edit-' . $user->id, $tbody);
            $this->assertContains('action="http://localhost/users/edit?id=' . $user->id . '"', $tbody);
        }
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
            $this->assertNotContains('<form class="edit-' . $user->id, $tbody);
            $this->assertNotContains('action="http://localhost/users/edit?id=' . $user->id . '"', $tbody);
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
            $this->assertNotContains('<form class="destroy-' . $user->id, $tbody);
            $this->assertNotContains('action="http://localhost/users/destroy?id=' . $user->id . '"', $tbody);
            $this->assertNotContains('data-target="#destroy-confirm-modal-' . $user->id . '"', $tbody);
            $this->assertNotContains(trans('tablelist::tablelist.modal.question', [
                'entity' => $user->{$table->destroyAttribute},
            ]), $tbody);
        }
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

    public function testDisableLineWithDefaultClassHtml()
    {
        config()->set('tablelist.value.disabled_line.class', 'test-disabled-default-class');
        $this->setRoutes(['users'], ['index', 'create', 'edit', 'destroy']);
        $routes = [
            'index'   => ['alias' => 'users.index', 'parameters' => []],
            'create'  => ['alias' => 'users.create', 'parameters' => []],
            'edit'    => ['alias' => 'users.edit', 'parameters' => []],
            'destroy' => ['alias' => 'users.destroy', 'parameters' => []],
        ];
        $users = $this->createMultipleUsers(5);
        $table = app(TableList::class)->setRoutes($routes)
            ->setModel(User::class)
            ->setRoutes($routes)
            ->disableLines(function($model) use ($users) {
                return $model->id === 1 || $model->id === 2;
            });
        $table->addColumn('name')->setTitle('Name')->sortByDefault()->useForDestroyConfirmation();
        $table->addColumn('email')->setTitle('Email');
        $table->render();
        foreach ($table->list->getCollection() as $user) {
            if ($user->id === 1 || $user->id === 2) {
                $this->assertTrue($user->disabled);
            } else {
                $this->assertFalse($user->disabled);
            }
        }
        $html = view('tablelist::tbody', ['table' => $table])->render();
        $this->assertContains('test-disabled-default-class', $html);
        $this->assertContains('disabled="disabled"', $html);
        $this->assertEquals(2, substr_count($html, 'test-disabled-default-class'));
        $this->assertEquals(4, substr_count($html, 'disabled="disabled"'));
    }

    public function testDisableLineWithCustomClassHtml()
    {
        $this->setRoutes(['users'], ['index', 'create', 'edit', 'destroy']);
        $routes = [
            'index'   => ['alias' => 'users.index', 'parameters' => []],
            'create'  => ['alias' => 'users.create', 'parameters' => []],
            'edit'    => ['alias' => 'users.edit', 'parameters' => []],
            'destroy' => ['alias' => 'users.destroy', 'parameters' => []],
        ];
        $users = $this->createMultipleUsers(5);
        $table = app(TableList::class)->setRoutes($routes)
            ->setModel(User::class)
            ->setRoutes($routes)
            ->disableLines(function($model) use ($users) {
                return $model->id === 1 || $model->id === 2;
            }, ['test-disabled-custom-class']);
        $table->addColumn('name')->setTitle('Name')->sortByDefault()->useForDestroyConfirmation();
        $table->addColumn('email')->setTitle('Email');
        $table->render();
        foreach ($table->list->getCollection() as $user) {
            if ($user->id === 1 || $user->id === 2) {
                $this->assertTrue($user->disabled);
            } else {
                $this->assertFalse($user->disabled);
            }
        }
        $html = view('tablelist::tbody', ['table' => $table])->render();
        $this->assertContains('test-disabled-custom-class', $html);
        $this->assertContains('disabled', $html);
        $this->assertContains('disabled="disabled"', $html);
        $this->assertEquals(2, substr_count($html, 'test-disabled-custom-class'));
        $this->assertEquals(14, substr_count($html, 'disabled'));
        $this->assertEquals(4, substr_count($html, 'disabled="disabled"'));
    }

    public function testWithNoDisableLinesHtml()
    {
        config()->set('tablelist.value.disabled_line.class', 'test-disabled-default-class');
        $this->setRoutes(['users'], ['index', 'create', 'edit', 'destroy']);
        $routes = [
            'index'   => ['alias' => 'users.index', 'parameters' => []],
            'create'  => ['alias' => 'users.create', 'parameters' => []],
            'edit'    => ['alias' => 'users.edit', 'parameters' => []],
            'destroy' => ['alias' => 'users.destroy', 'parameters' => []],
        ];
        $this->createMultipleUsers(5);
        $table = app(TableList::class)->setRoutes($routes)
            ->setModel(User::class)
            ->setRoutes($routes);
        $table->addColumn('name')->setTitle('Name')->sortByDefault()->useForDestroyConfirmation();
        $table->addColumn('email')->setTitle('Email');
        $table->render();
        $html = view('tablelist::tbody', ['table' => $table])->render();
        $this->assertNotContains('test-disabled-default-class', $html);
        $this->assertNotContains('disabled="disabled"', $html);
        $this->assertEquals(0, substr_count($html, 'test-disabled-default-class'));
        $this->assertEquals(0, substr_count($html, 'disabled="disabled"'));
    }

    public function testHighlightLinesWithDefaultClassHtml()
    {
        config()->set('tablelist.value.highlighted_line.class', 'test-highlighted-default-class');
        $this->setRoutes(['users'], ['index', 'create', 'edit', 'destroy']);
        $routes = [
            'index'   => ['alias' => 'users.index', 'parameters' => []],
            'create'  => ['alias' => 'users.create', 'parameters' => []],
            'edit'    => ['alias' => 'users.edit', 'parameters' => []],
            'destroy' => ['alias' => 'users.destroy', 'parameters' => []],
        ];
        $users = $this->createMultipleUsers(5);
        $table = app(TableList::class)->setRoutes($routes)
            ->setModel(User::class)
            ->setRoutes($routes)
            ->highlightLines(function($model) use ($users) {
                return $model->id === 1 || $model->id === 2;
            });
        $table->addColumn('name')->setTitle('Name')->sortByDefault()->useForDestroyConfirmation();
        $table->addColumn('email')->setTitle('Email');
        $table->render();
        foreach ($table->list->getCollection() as $user) {
            if ($user->id === 1 || $user->id === 2) {
                $this->assertTrue($user->highlighted);
            } else {
                $this->assertFalse($user->highlighted);
            }
        }
        $html = view('tablelist::table', ['table' => $table])->render();
        $this->assertContains('test-highlighted-default-class', $html);
        $this->assertEquals(2, substr_count($html, 'test-highlighted-default-class'));
    }

    public function testHighlightLinesWithCustomClassHtml()
    {
        $this->setRoutes(['users'], ['index', 'create', 'edit', 'destroy']);
        $routes = [
            'index'   => ['alias' => 'users.index', 'parameters' => []],
            'create'  => ['alias' => 'users.create', 'parameters' => []],
            'edit'    => ['alias' => 'users.edit', 'parameters' => []],
            'destroy' => ['alias' => 'users.destroy', 'parameters' => []],
        ];
        $users = $this->createMultipleUsers(5);
        $table = app(TableList::class)->setRoutes($routes)
            ->setModel(User::class)
            ->setRoutes($routes)
            ->highlightLines(function($model) use ($users) {
                return $model->id === 1 || $model->id === 2;
            }, ['test-highlighted-custom-class']);
        $table->addColumn('name')->setTitle('Name')->sortByDefault()->useForDestroyConfirmation();
        $table->addColumn('email')->setTitle('Email');
        $table->render();
        foreach ($table->list->getCollection() as $user) {
            if ($user->id === 1 || $user->id === 2) {
                $this->assertTrue($user->highlighted);
            } else {
                $this->assertFalse($user->highlighted);
            }
        }
        $html = view('tablelist::table', ['table' => $table])->render();
        $this->assertContains('test-highlighted-custom-class', $html);
        $this->assertEquals(2, substr_count($html, 'test-highlighted-custom-class'));
    }

    public function testNoHighlightedLinesHtml()
    {
        config()->set('tablelist.value.highlighted_line.class', 'test-highlighted-default-class');
        $this->setRoutes(['users'], ['index', 'create', 'edit', 'destroy']);
        $routes = [
            'index'   => ['alias' => 'users.index', 'parameters' => []],
            'create'  => ['alias' => 'users.create', 'parameters' => []],
            'edit'    => ['alias' => 'users.edit', 'parameters' => []],
            'destroy' => ['alias' => 'users.destroy', 'parameters' => []],
        ];
        $this->createMultipleUsers(5);
        $table = app(TableList::class)->setRoutes($routes)
            ->setModel(User::class)
            ->setRoutes($routes);
        $table->addColumn('name')->setTitle('Name')->sortByDefault()->useForDestroyConfirmation();
        $table->addColumn('email')->setTitle('Email');
        $table->render();
        $html = view('tablelist::table', ['table' => $table])->render();
        $this->assertNotContains('test-highlighted-default-class', $html);
        $this->assertEquals(0, substr_count($html, 'test-highlighted-default-class'));
    }

    public function testSetTitleHtml()
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

    public function testSetStringLimitHtml()
    {
        $this->setRoutes(['users'], ['index']);
        $routes = [
            'index' => ['alias' => 'users.index', 'parameters' => []],
        ];
        $user = $this->createUniqueUser();
        $table = app(TableList::class)->setRoutes($routes)
            ->setModel(User::class)
            ->setRoutes($routes);
        $table->addColumn('name')->setTitle('Name')->sortByDefault()->useForDestroyConfirmation();
        $table->addColumn('email')->setTitle('Email')->setStringLimit(2);
        $html = $table->render();
        $this->assertContains(str_limit($user->email, 2), $html);
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
        $this->assertContains('<div class="search-bar', $thead);
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

    public function testNoSearchableHtml()
    {
        $this->setRoutes(['users'], ['index']);
        $routes = ['index' => ['alias' => 'users.index', 'parameters' => []]];
        $table = app(TableList::class)->setRoutes($routes)->setModel(User::class);
        $table->addColumn('name')->setTitle('Name')->sortByDefault('desc');
        $table->addColumn('email')->setTitle('Email');
        $table->render();
        $thead = view('tablelist::thead', ['table' => $table])->render();
        $this->assertNotContains('<div class="search-bar', $thead);
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

    public function testMultipleDestroyConfirmationDisplayedInModalHtml()
    {
        $users = $this->createMultipleUsers(5);
        $this->setRoutes(['users'], ['destroy']);
        $routes = [
            'index'   => ['alias' => 'users.index', 'parameters' => []],
            'destroy' => ['alias' => 'users.destroy', 'parameters' => []],
        ];
        $table = app(TableList::class)->setRoutes($routes)->setModel(User::class);
        $table->addColumn('name')->setTitle('Name')->sortByDefault()->useForDestroyConfirmation();
        $table->addColumn('email')->setTitle('Email')->useForDestroyConfirmation();
        $table->render();
        $tbody = view('tablelist::tbody', ['table' => $table])->render();
        foreach ($users as $user) {
            $this->assertContains('<form class="destroy-' . $user->id, $tbody);
            $this->assertContains('action="http://localhost/users/destroy?id=' . $user->id . '"', $tbody);
            $this->assertContains('data-target="#destroy-confirm-modal-' . $user->id . '"', $tbody);
            $this->assertContains(trans('tablelist::tablelist.modal.question', [
                'entity' => $table->destroyAttributes->map(function($attribute) use ($user) {
                    return $user->{$attribute};
                })->implode(' '),
            ]), $tbody);
        }
    }

    public function testIsButtonHtml()
    {
        $users = $this->createMultipleUsers(1);
        $this->setRoutes(['users'], ['index']);
        $routes = [
            'index' => ['alias' => 'users.index', 'parameters' => []],
        ];
        $table = app(TableList::class)->setRoutes($routes)->setModel(User::class);
        $table->addColumn('name')->sortByDefault()->useForDestroyConfirmation()->isButton(['btn', 'btn-primary']);
        $table->render();
        $html = view('tablelist::tbody', ['table' => $table])->render();
        $this->assertContains('<button class="btn btn-primary ' . str_slug($users->first()->name, '-') . '">', $html);
    }

    public function testIsLinkDefaultHtml()
    {
        $users = $this->createMultipleUsers(1);
        $this->setRoutes(['users'], ['index']);
        $routes = [
            'index' => ['alias' => 'users.index', 'parameters' => []],
        ];
        $table = app(TableList::class)->setRoutes($routes)->setModel(User::class);
        $table->addColumn('name')->sortByDefault()->useForDestroyConfirmation()->isLink();
        $table->render();
        $html = view('tablelist::tbody', ['table' => $table])->render();
        $this->assertContains('<a href="' . $users->first()->name . '" title="validation.attributes.name">', $html);
    }

    public function testIsLinkStringHtml()
    {
        $this->createMultipleUsers(1);
        $this->setRoutes(['users'], ['index']);
        $routes = [
            'index' => ['alias' => 'users.index', 'parameters' => []],
        ];
        $table = app(TableList::class)->setRoutes($routes)->setModel(User::class);
        $table->addColumn('name')->sortByDefault()->useForDestroyConfirmation()->isLink('test');
        $table->render();
        $html = view('tablelist::tbody', ['table' => $table])->render();
        $this->assertContains('<a href="test" title="validation.attributes.name">', $html);
    }

    public function testIsLinkClosureHtml()
    {
        $this->createMultipleUsers(1);
        $this->setRoutes(['users'], ['index']);
        $routes = [
            'index' => ['alias' => 'users.index', 'parameters' => []],
        ];
        $table = app(TableList::class)->setRoutes($routes)->setModel(User::class);
        $table->addColumn('name')->sortByDefault()->useForDestroyConfirmation()->isLink(function(){
            return 'url';
        });
        $table->render();
        $html = view('tablelist::tbody', ['table' => $table])->render();
        $this->assertContains('<a href="url" title="validation.attributes.name">', $html);
    }

    public function testSetIconHtml()
    {
        $this->createMultipleUsers(1);
        $this->setRoutes(['users'], ['index']);
        $routes = [
            'index' => ['alias' => 'users.index', 'parameters' => []],
        ];
        $table = app(TableList::class)->setRoutes($routes)->setModel(User::class);
        $table->addColumn('name')->sortByDefault()->useForDestroyConfirmation()->setIcon('icon');
        $table->render();
        $html = view('tablelist::tbody', ['table' => $table])->render();
        $this->assertContains('icon', $html);
    }
}
