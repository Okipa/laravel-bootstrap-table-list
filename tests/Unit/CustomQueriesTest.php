<?php

namespace Okipa\LaravelBootstrapTableList\Tests\Unit;

use Illuminate\Http\Request;
use Okipa\LaravelBootstrapTableList\TableList;
use Okipa\LaravelBootstrapTableList\Test\Models\Company;
use Okipa\LaravelBootstrapTableList\Test\Models\User;
use Okipa\LaravelBootstrapTableList\Test\TableListTestCase;

class SetCustomTableTest extends TableListTestCase
{
    public function testSetAddQueryInstructionsAttribute()
    {
        $queryClosure = function ($query) {
            $query->select('users.*')->where('users.activated');
        };
        $table = app(TableList::class)->addQueryInstructions($queryClosure);
        $this->assertEquals($queryClosure, $table->queryClosure);
    }

    public function testSetCustomTableAttributeOnly()
    {
        $table = app(TableList::class)->setModel(User::class);
        $table->addColumn('name')->setCustomTable('custom_table');
        $this->assertEquals('custom_table', $table->columns->first()->customColumnTable);
    }

    public function testSetCustomTableAndAliasAttributes()
    {
        $table = app(TableList::class)->setModel(User::class);
        $table->addColumn('name')->setCustomTable('custom_table', 'real_field');
        $this->assertEquals('custom_table', $table->columns->first()->customColumnTable);
        $this->assertEquals('real_field', $table->columns->first()->customColumnTableRealAttribute);
    }

    /**
     * @expectedException \ErrorException
     * @expectedExceptionMessage The given searchable column attribute « owner » does not exist in the « companies_test
     *                           » table. Set the correct column-related table and alias with the « setCustomTable() »
     *                           method.
     */
    public function testSearchOnOtherTableFieldWithoutCustomTableDeclaration()
    {
        $this->createMultipleUsers(5);
        $this->createMultipleCompanies(2);
        $this->setRoutes(['companies'], ['index']);
        $routes = [
            'index' => ['alias' => 'companies.index', 'parameters' => []],
        ];
        $table = app(TableList::class)->setRoutes($routes)
            ->setModel(Company::class)
            ->addQueryInstructions(function ($query) {
                $query->select('companies_test.*');
                $query->addSelect('users_test.name as owner');
                $query->join('users_test', 'users_test.id', '=', 'companies_test.owner_id');
            });
        $table->addColumn('owner')->isSearchable();
        $table->render();
    }

    /**
     * @expectedException \ErrorException
     * @expectedExceptionMessage One of the searchable columns has no defined attribute. You have to define a column
     *                           attribute for each searchable columns by setting a string parameter in the «
     *                           addColumn() » method.
     */
    public function testSearchOnOtherTableFieldWithCustomTableDeclarationWithoutColumnAttribute()
    {
        $this->createMultipleUsers(5);
        $this->createMultipleCompanies(2);
        $this->setRoutes(['companies'], ['index']);
        $routes = [
            'index' => ['alias' => 'companies.index', 'parameters' => []],
        ];
        $table = app(TableList::class)->setRoutes($routes)
            ->setModel(Company::class)
            ->addQueryInstructions(function ($query) {
                $query->select('companies_test.*');
                $query->addSelect('users_test.name as owner');
                $query->join('users_test', 'users_test.id', '=', 'companies_test.owner_id');
            });
        $table->addColumn()->setCustomTable('users_test', 'name')->isSearchable();
        $table->render();
    }

    /**
     * @expectedException \ErrorException
     * @expectedExceptionMessage The given searchable column attribute « owner » does not exist in the « users_test »
     *                           table. Set the correct column-related table and alias with the « setCustomTable() »
     *                           method.
     */
    public function testSearchOnOtherTableFieldWithCustomTableDeclarationWithoutAlias()
    {
        $this->createMultipleUsers(5);
        $this->createMultipleCompanies(2);
        $this->setRoutes(['companies'], ['index']);
        $routes = [
            'index' => ['alias' => 'companies.index', 'parameters' => []],
        ];
        $table = app(TableList::class)->setRoutes($routes)
            ->setModel(Company::class)
            ->addQueryInstructions(function ($query) {
                $query->select('companies_test.*');
                $query->addSelect('users_test.name as owner');
                $query->join('users_test', 'users_test.id', '=', 'companies_test.owner_id');
            });
        $table->addColumn('owner')->setCustomTable('users_test')->isSearchable();
        $table->render();
    }

    public function testSearchOnOtherTableFieldWithCustomTableDeclarationHtml()
    {
        $this->createMultipleUsers(5);
        $companies = $this->createMultipleCompanies(2);
        $this->setRoutes(['companies'], ['index']);
        $routes = [
            'index' => ['alias' => 'companies.index', 'parameters' => []],
        ];
        $searchedValue = $companies->first()->owner->name;
        $customRequest = app(Request::class);
        $customRequest->merge([
            'rowsNumber' => 20,
            'search'     => $searchedValue,
        ]);
        $table = app(TableList::class)->setRoutes($routes)
            ->setModel(Company::class)
            ->addQueryInstructions(function ($query) {
                $query->select('companies_test.*');
                $query->addSelect('users_test.name as owner');
                $query->join('users_test', 'users_test.id', '=', 'companies_test.owner_id');
            })
            ->setRequest($customRequest);
        $table->addColumn('owner')->setCustomTable('users_test', 'name')->isSearchable();
        $table->render();
        $html = view('tablelist::tbody', ['table' => $table])->render();
        foreach ($companies as $company) {
            if ($company->owner->name === $searchedValue) {
                $this->assertContains($company->owner->name, $html);
            } else {
                $this->assertNotContains($company->owner->name, $html);
            }
        }
    }

    public function testPaginateSearchOnOtherTableField()
    {
        $users = $this->createMultipleUsers(1);
        $this->createMultipleCompanies(10);
        $this->setRoutes(['companies'], ['index']);
        $routes = [
            'index' => ['alias' => 'companies.index', 'parameters' => []],
        ];
        $searchedValue = $users->first()->name;
        $customRequest = app(Request::class);
        $customRequest->merge([
            'rowsNumber' => 5,
            'search'     => $searchedValue,
            'page'       => 2,
        ]);
        $table = app(TableList::class)->setRoutes($routes)
            ->setModel(Company::class)
            ->addQueryInstructions(function ($query) {
                $query->select('companies_test.*');
                $query->addSelect('users_test.name as owner');
                $query->join('users_test', 'users_test.id', '=', 'companies_test.owner_id');
            })
            ->setRequest($customRequest);
        $table->addColumn('owner')->setCustomTable('users_test', 'name')->isSearchable();
        $table->render();
        foreach (App(Company::class)->paginate(5) as $key => $company) {
            $this->assertEquals($company->name, $table->list->toArray()['data'][$key]['name']);
        }
    }

    public function testSortOnOtherTableFieldWithoutCustomTableDeclaration()
    {
        $this->createMultipleUsers(5);
        $companies = $this->createMultipleCompanies(5);
        $this->setRoutes(['companies'], ['index']);
        $routes = [
            'index' => ['alias' => 'companies.index', 'parameters' => []],
        ];
        $customRequest = app(Request::class);
        $customRequest->merge([
            'rowsNumber' => 20,
            'sortBy'     => 'owner',
            'sortDir'    => 'desc',
        ]);
        $table = app(TableList::class)->setRoutes($routes)
            ->setModel(Company::class)
            ->addQueryInstructions(function ($query) {
                $query->select('companies_test.*');
                $query->addSelect('users_test.name as owner');
                $query->join('users_test', 'users_test.id', '=', 'companies_test.owner_id');
            })
            ->setRequest($customRequest);
        $table->addColumn('owner')->isSortable();
        $table->render();
        foreach ($companies->load('owner')->sortByDesc('owner.name')->values() as $key => $company) {
            $this->assertEquals($company->owner->name, $table->list->toArray()['data'][$key]['owner']);
        }
    }

    public function testPaginateSortOnOtherTableField()
    {
        $this->createMultipleUsers(5);
        $this->createMultipleCompanies(10);
        $this->setRoutes(['companies'], ['index']);
        $routes = [
            'index' => ['alias' => 'companies.index', 'parameters' => []],
        ];
        $customRequest = app(Request::class);
        $customRequest->merge([
            'rowsNumber' => 5,
            'sortBy'     => 'owner',
            'sortDir'    => 'desc',
        ]);
        $table = app(TableList::class)->setRoutes($routes)
            ->setModel(Company::class)
            ->addQueryInstructions(function ($query) {
                $query->select('companies_test.*');
                $query->addSelect('users_test.name as owner');
                $query->join('users_test', 'users_test.id', '=', 'companies_test.owner_id');
            })
            ->setRequest($customRequest);
        $table->addColumn('owner')->isSortable();
        $table->render();
        $paginatedCompanies = Company::join('users_test', 'users_test.id', '=', 'companies_test.owner_id')
            ->orderBy('users_test.name', 'desc')
            ->select('companies_test.*')
            ->with('owner')
            ->paginate(5);
        foreach ($paginatedCompanies as $key => $company) {
            $this->assertEquals($company->owner->name, $table->list->toArray()['data'][$key]['owner']);
        }
    }
}
