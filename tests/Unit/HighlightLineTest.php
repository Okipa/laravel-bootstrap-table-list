<?php

namespace Okipa\LaravelBootstrapTableList\Tests\Unit;

use Okipa\LaravelBootstrapTableList\TableList;
use Okipa\LaravelBootstrapTableList\Test\Models\User;
use Okipa\LaravelBootstrapTableList\Test\TableListTestCase;

class HighlightLineTest extends TableListTestCase
{
    public function testHighlightLinesAttribute()
    {
        $highlightLinesClosure = function ($model) {
            return $model->id === 1;
        };
        $highlightedLinesClass = ['test-highlighted-custom-class'];
        $table = app(TableList::class)->highlightLines($highlightLinesClosure, $highlightedLinesClass);
        $this->assertEquals($highlightLinesClosure, $table->highlightLinesClosure);
        $this->assertEquals($highlightedLinesClass, $table->highlightLinesClass);
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
            ->highlightLines(function ($model) use ($users) {
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
            ->highlightLines(function ($model) use ($users) {
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
}
