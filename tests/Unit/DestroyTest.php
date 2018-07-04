<?php

namespace Okipa\LaravelBootstrapTableList\Tests\Unit;

use Okipa\LaravelBootstrapTableList\TableList;
use Okipa\LaravelBootstrapTableList\Test\Fakers\RoutesFaker;
use Okipa\LaravelBootstrapTableList\Test\Fakers\UsersFaker;
use Okipa\LaravelBootstrapTableList\Test\Models\User;
use Okipa\LaravelBootstrapTableList\Test\TableListTestCase;

class DestroyTest extends TableListTestCase
{
    use RoutesFaker;
    use UsersFaker;

    public function testDestroyActionHtmlWithModal()
    {
        config()->set('tablelist.template.table.tbody.destroy.trigger-bootstrap-modal', true);
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
            $this->assertContains('<form class="destroy-' . $user->id, $tbody);
            $this->assertContains('action="http://localhost/users/destroy?id=' . $user->id . '"', $tbody);
            $this->assertContains('data-target="#destroy-confirm-modal-' . $user->id . '"', $tbody);
            $this->assertContains(trans('tablelist::tablelist.modal.question', [
                'entity' => $user->{$table->destroyAttribute},
            ]), $tbody);
        }
    }

    public function testDestroyActionHtmlWithoutModal()
    {
        config()->set('tablelist.template.table.tbody.destroy.trigger-bootstrap-modal', false);
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
            $this->assertContains('<form class="destroy-' . $user->id, $tbody);
            $this->assertContains('action="http://localhost/users/destroy?id=' . $user->id . '"', $tbody);
            $this->assertNotContains('data-target="#destroy-confirm-modal-' . $user->id . '"', $tbody);
            $this->assertNotContains(trans('tablelist::tablelist.modal.question', [
                'entity' => $user->{$table->destroyAttribute},
            ]), $tbody);
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
}