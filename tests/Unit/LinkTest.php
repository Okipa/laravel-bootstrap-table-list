<?php

namespace Okipa\LaravelBootstrapTableList\Tests\Unit;

use Okipa\LaravelBootstrapTableList\TableList;
use Okipa\LaravelBootstrapTableList\Test\Models\User;
use Okipa\LaravelBootstrapTableList\Test\TableListTestCase;

class LinkTest extends TableListTestCase
{
    public function testSetIsLinkAttributeEmpty()
    {
        $table = app(TableList::class)->setModel(User::class);
        $table->addColumn('name')->isLink();
        $this->assertEquals(true, $table->columns->first()->url);
    }

    public function testSetIsLinkAttributeString()
    {
        $table = app(TableList::class)->setModel(User::class);
        $table->addColumn('name')->isLink('link');
        $this->assertEquals('link', $table->columns->first()->url);
    }

    public function testSetIsLinkAttributeClosure()
    {
        $table = app(TableList::class)->setModel(User::class);
        $closure = function ($entity, $column) {
        };
        $table->addColumn('name')->isLink($closure);
        $this->assertEquals($closure, $table->columns->first()->url);
    }

    public function testIsLinkEmptyHtml()
    {
        $user = $this->createUniqueUser();
        $this->setRoutes(['users'], ['index']);
        $routes = [
            'index' => ['alias' => 'users.index', 'parameters' => []],
        ];
        $table = app(TableList::class)->setRoutes($routes)->setModel(User::class);
        $table->addColumn('name')->sortByDefault()->useForDestroyConfirmation()->isLink();
        $table->render();
        $html = view('tablelist::tbody', ['table' => $table])->render();
        $this->assertContains('<a href="' . $user->name . '" title="validation.attributes.name">', $html);
    }

    public function testIsLinkStringHtml()
    {
        $this->createUniqueUser();
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
        $this->createUniqueUser();
        $this->setRoutes(['users'], ['index']);
        $routes = [
            'index' => ['alias' => 'users.index', 'parameters' => []],
        ];
        $table = app(TableList::class)->setRoutes($routes)->setModel(User::class);
        $table->addColumn('name')->sortByDefault()->useForDestroyConfirmation()->isLink(function () {
            return 'url';
        });
        $table->render();
        $html = view('tablelist::tbody', ['table' => $table])->render();
        $this->assertContains('<a href="url" title="validation.attributes.name">', $html);
    }

    public function testIsLinkWithNoValueHtml()
    {
        $user = $this->createUniqueUser();
        $user->update(['name' => null]);
        $this->setRoutes(['users'], ['index']);
        $routes = [
            'index' => ['alias' => 'users.index', 'parameters' => []],
        ];
        $table = app(TableList::class)->setRoutes($routes)->setModel(User::class);
        $table->addColumn('name')->sortByDefault()->useForDestroyConfirmation()->isLink();
        $table->render();
        $html = view('tablelist::tbody', ['table' => $table])->render();
        $this->assertNotContains('<a href="' . $user->name . '" title="validation.attributes.name">', $html);
    }

    public function testIsLinkWithNoValueWithIconHtml()
    {
        $user = $this->createUniqueUser();
        $user->update(['name' => null]);
        $this->setRoutes(['users'], ['index']);
        $routes = [
            'index' => ['alias' => 'users.index', 'parameters' => []],
        ];
        $table = app(TableList::class)->setRoutes($routes)->setModel(User::class);
        $table->addColumn('name')->sortByDefault()->useForDestroyConfirmation()->isLink()->setIcon('icon', true);
        $table->render();
        $html = view('tablelist::tbody', ['table' => $table])->render();
        $this->assertContains('<a href="' . $user->name . '" title="validation.attributes.name">', $html);
    }
}
