<?php

namespace Okipa\LaravelBootstrapTableList\Tests\Unit;

use ErrorException;
use Okipa\LaravelBootstrapTableList\TableList;
use Okipa\LaravelBootstrapTableList\Test\Models\User;
use Okipa\LaravelBootstrapTableList\Test\TableListTestCase;

class DestroyConfirmationTest extends TableListTestCase
{
    public function testUseForDestroyConfirmationSingle()
    {
        $table = app(TableList::class)->setModel(User::class);
        $table->addColumn('name')->useForDestroyConfirmation();
        $this->assertEquals(['name'], $table->destroyAttributes->toArray());
    }

    public function testUseForDestroyConfirmationMultiple()
    {
        $table = app(TableList::class)->setModel(User::class);
        $table->addColumn('name')->useForDestroyConfirmation();
        $table->addColumn('email')->useForDestroyConfirmation();
        $this->assertEquals(['name', 'email'], $table->destroyAttributes->toArray());
    }
    
    /**
     * @expectedException ErrorException
     * @expectedExceptionMessage No columns have been chosen for the destroy confirmation. Use the «
     *                            useForDestroyConfirmation() » method on column objects to define them.
     */
    public function testNoDeclaredDestroyConfirmationAttribute()
    {
        $routes = [
            'index'   => ['alias' => 'users.index', 'parameters' => []],
            'destroy' => ['alias' => 'users.destroy', 'parameters' => []],
        ];
        $table = app(TableList::class)->setRoutes($routes)->setModel(User::class);
        $table->addColumn('name')->setTitle('Name');
        $table->render();
    }

    public function testMultipleDestroyConfirmationHtml()
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
                'entity' => $table->destroyAttributes->map(function ($attribute) use ($user) {
                    return $user->{$attribute};
                })->implode(' '),
            ]), $tbody);
        }
    }
}
