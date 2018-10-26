<?php

namespace Okipa\LaravelBootstrapTableList\Tests\Unit;

use Okipa\LaravelBootstrapTableList\TableList;
use Okipa\LaravelBootstrapTableList\Test\Models\User;
use Okipa\LaravelBootstrapTableList\Test\TableListTestCase;

class DisableLineTest extends TableListTestCase
{
    public function testSetDisableLinesAttribute()
    {
        $disableLinesClosure = function ($model) {
            return $model->id === 1;
        };
        $disabledLinesClass = ['test-disabled-custom-class'];
        $table = app(TableList::class)->disableLines($disableLinesClosure, $disabledLinesClass);
        $this->assertEquals($disableLinesClosure, $table->disableLinesClosure);
        $this->assertEquals($disabledLinesClass, $table->disableLinesClass);
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
            ->disableLines(function ($model) use ($users) {
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
            ->disableLines(function ($model) use ($users) {
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
}
