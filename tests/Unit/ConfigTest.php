<?php

namespace Okipa\LaravelBootstrapTableList\Tests\Unit;

use Illuminate\Http\Request;
use Okipa\LaravelBootstrapTableList\TableList;
use Okipa\LaravelBootstrapTableList\Test\Fakers\RoutesFaker;
use Okipa\LaravelBootstrapTableList\Test\Fakers\UsersFaker;
use Okipa\LaravelBootstrapTableList\Test\Models\User;
use Okipa\LaravelBootstrapTableList\Test\TableListTestCase;

class ConfigTest extends TableListTestCase
{
    use RoutesFaker;
    use UsersFaker;

    protected function generateTableList(Request $request = null, array $routes = null)
    {
        $this->createMultipleUsers(3);
        $this->setRoutes(['users'], ['index', 'create', 'edit', 'destroy']);
        $table = app(TableList::class)
            ->setModel(User::class)
            ->enableRowsNumberSelector();
        if (! empty($routes)) {
            $table->setRoutes($routes);
        } else {
            $table->setRoutes([
                'index'   => ['alias' => 'users.index', 'parameters' => []],
                'create'  => ['alias' => 'users.create', 'parameters' => []],
                'edit'    => ['alias' => 'users.edit', 'parameters' => []],
                'destroy' => ['alias' => 'users.destroy', 'parameters' => []],
            ]);
        }
        if ($request) {
            $table->setRequest($request);
        }
        $table->addColumn('name')
            ->setTitle('Name')
            ->sortByDefault()
            ->isSortable()
            ->isSearchable()
            ->useForDestroyConfirmation();;
        $table->addColumn('email')
            ->setTitle('Email')
            ->isSearchable()
            ->isSortable();
        $table->render();

        return view('tablelist::table', ['table' => $table])->render();
    }

    public function testCustomValueRowsNumber()
    {
        config()->set('tablelist.value.rows_number', 9999);
        $this->createMultipleUsers(3);
        $this->setRoutes(['users'], ['index', 'create', 'edit', 'destroy']);
        $table = app(TableList::class)
            ->setModel(User::class)
            ->enableRowsNumberSelector()
            ->setRoutes([
                'index'   => ['alias' => 'users.index', 'parameters' => []],
                'create'  => ['alias' => 'users.create', 'parameters' => []],
                'edit'    => ['alias' => 'users.edit', 'parameters' => []],
                'destroy' => ['alias' => 'users.destroy', 'parameters' => []],
            ]);
        $table->addColumn('name')
            ->setTitle('Name')
            ->sortByDefault()
            ->isSortable()
            ->isSearchable()
            ->useForDestroyConfirmation();;
        $table->addColumn('email')
            ->setTitle('Email')
            ->isSearchable()
            ->isSortable();
        $table->render();
        $html = view('tablelist::table', ['table' => $table])->render();
        $this->assertEquals(9999, $table->rowsNumber);
        $this->assertContains('value="9999"', $html);
        $this->assertContains('rowsNumber=9999', $html);
    }

    public function testCustomTemplateTableConfig()
    {
        $containerClass = 'test-table-container-custom-class';
        $itemClass = 'test-table-item-custom-class';
        $trClass = 'test-table-tr-custom-class';
        $tdClass = 'test-table-td-custom-class';
        $thClass = 'test-table-th-custom-class';
        config()->set('tablelist.template.table.container.class', $containerClass);
        config()->set('tablelist.template.table.item.class', $itemClass);
        config()->set('tablelist.template.table.tr.class', $trClass);
        config()->set('tablelist.template.table.th.class', $thClass);
        config()->set('tablelist.template.table.td.class', $tdClass);
        $html = $this->generateTableList();
        $this->assertContains('<div class="table-list-container ' . $containerClass . '">', $html);
        $this->assertContains('<table class="table ' . $itemClass . '">', $html);
        $this->assertEquals(substr_count($html, '<tr class='), substr_count($html, $trClass));
        $this->assertEquals(substr_count($html, '<th class='), substr_count($html, $thClass));
        $this->assertEquals(substr_count($html, '<td class='), substr_count($html, $tdClass));
    }

    public function testCustomTemplateTableTheadConfig()
    {
        $itemClass = 'test-table-thead-item-custom-class';
        config()->set('tablelist.template.table.thead.item.class', $itemClass);
        $html = $this->generateTableList();
        $this->assertContains($itemClass, $html);
        $this->assertEquals(1, substr_count($html, $itemClass));
    }

    public function testCustomTemplateTableTheadOptionsBarConfig()
    {
        $trClass = 'test-table-thead-options-bar-tr-custom-class';
        $tdClass = 'test-table-thead-options-bar-td-custom-class';
        config()->set('tablelist.template.table.thead.options-bar.tr.class', $trClass);
        config()->set('tablelist.template.table.thead.options-bar.td.class', $tdClass);
        $html = $this->generateTableList();
        $this->assertEquals(1, substr_count($html, $trClass));
        $this->assertEquals(1, substr_count($html, $tdClass));
    }

    public function testCustomTemplateTableTheadOptionsBarRowsNumberSelectorConfig()
    {
        $itemClass = 'test-table-thead-options-bar-rows-number-selector-item-custom-class';
        config()->set('tablelist.template.table.thead.options-bar.rows-number-selector.item.class', $itemClass);
        $html = $this->generateTableList();
        $this->assertContains($itemClass, $html);
        $this->assertEquals(1, substr_count($html, $itemClass));
    }

    public function testCustomTemplateTableTheadOptionsBarRowsNumberSelectorLinesConfig()
    {
        $containerClass = 'test-table-thead-options-bar-rows-number-selector-lines-container-custom-class';
        $itemClass = 'test-table-thead-options-bar-rows-number-selector-lines-item-custom-class';
        $itemIcon = 'test-table-thead-options-bar-rows-number-selector-lines-item-custom-icon';
        config()->set('tablelist.template.table.thead.options-bar.rows-number-selector.lines.container.class',
            $containerClass);
        config()->set('tablelist.template.table.thead.options-bar.rows-number-selector.lines.item.class', $itemClass);
        config()->set('tablelist.template.table.thead.options-bar.rows-number-selector.lines.item.icon', $itemIcon);
        $html = $this->generateTableList();
        $this->assertContains($containerClass, $html);
        $this->assertEquals(1, substr_count($html, $containerClass));
        $this->assertContains($itemClass, $html);
        $this->assertEquals(1, substr_count($html, $itemClass));
        $this->assertContains($itemIcon, $html);
        $this->assertEquals(1, substr_count($html, $itemIcon));
    }

    public function testCustomTemplateTableTheadOptionsBarRowsNumberSelectorValidateConfig()
    {
        $containerClass = 'test-table-thead-options-bar-rows-number-selector-validate-container-custom-class';
        $itemClass = 'test-table-thead-options-bar-rows-number-selector-validate-item-custom-class';
        $itemIcon = 'test-table-thead-options-bar-rows-number-selector-validate-item-custom-icon';
        config()->set('tablelist.template.table.thead.options-bar.rows-number-selector.validate.container.class',
            $containerClass);
        config()->set('tablelist.template.table.thead.options-bar.rows-number-selector.validate.item.class',
            $itemClass);
        config()->set('tablelist.template.table.thead.options-bar.rows-number-selector.validate.item.icon', $itemIcon);
        $html = $this->generateTableList();
        $this->assertContains($containerClass, $html);
        $this->assertEquals(1, substr_count($html, $containerClass));
        $this->assertContains($itemClass, $html);
        $this->assertEquals(1, substr_count($html, $itemClass));
        $this->assertContains($itemIcon, $html);
        $this->assertEquals(1, substr_count($html, $itemIcon));
    }

    public function testCustomTemplateTableTheadOptionsBarSpacerConfig()
    {
        $itemClass = 'test-table-thead-options-bar-spacer-item-custom-class';
        config()->set('tablelist.template.table.thead.options-bar.spacer.item.class', $itemClass);
        $html = $this->generateTableList();
        $this->assertContains($itemClass, $html);
        $this->assertEquals(1, substr_count($html, $itemClass));
    }

    public function testCustomTemplateTableTheadOptionsBarSearchBarConfig()
    {
        $itemClass = 'test-table-thead-options-bar-search-bar-item-custom-class';
        config()->set('tablelist.template.table.thead.options-bar.spacer.item.class', $itemClass);
        $html = $this->generateTableList();
        $this->assertContains($itemClass, $html);
        $this->assertEquals(1, substr_count($html, $itemClass));
    }

    public function testCustomTemplateTableTheadOptionsBarSearchBarSearchConfig()
    {
        $containerClass = 'test-table-thead-options-bar-search-bar-search-container-custom-class';
        $itemClass = 'test-table-thead-options-bar-search-bar-search-item-custom-class';
        $itemIcon = 'test-table-thead-options-bar-search-bar-search-item-custom-icon';
        config()->set('tablelist.template.table.thead.options-bar.search-bar.search.container.class', $containerClass);
        config()->set('tablelist.template.table.thead.options-bar.search-bar.search.item.class', $itemClass);
        config()->set('tablelist.template.table.thead.options-bar.search-bar.search.item.icon', $itemIcon);
        $html = $this->generateTableList();
        $this->assertContains($containerClass, $html);
        $this->assertEquals(1, substr_count($html, $containerClass));
        $this->assertContains($itemClass, $html);
        $this->assertEquals(1, substr_count($html, $itemClass));
        $this->assertContains($itemIcon, $html);
        $this->assertEquals(1, substr_count($html, $itemIcon));
    }

    public function testCustomTemplateTableTheadOptionsBarSearchBarValidateConfig()
    {
        $containerClass = 'test-table-thead-options-bar-search-bar-validate-container-custom-class';
        $itemClass = 'test-table-thead-options-bar-search-bar-validate-item-custom-class';
        $itemIcon = 'test-table-thead-options-bar-search-bar-validate-item-custom-icon';
        config()->set('tablelist.template.table.thead.options-bar.search-bar.validate.container.class',
            $containerClass);
        config()->set('tablelist.template.table.thead.options-bar.search-bar.validate.item.class', $itemClass);
        config()->set('tablelist.template.table.thead.options-bar.search-bar.validate.item.icon', $itemIcon);
        $html = $this->generateTableList();
        $this->assertContains($containerClass, $html);
        $this->assertEquals(1, substr_count($html, $containerClass));
        $this->assertContains($itemClass, $html);
        $this->assertEquals(1, substr_count($html, $itemClass));
        $this->assertContains($itemIcon, $html);
        $this->assertEquals(1, substr_count($html, $itemIcon));
    }

    public function testCustomTemplateTableTheadOptionsBarSearchCancelConfig()
    {
        $containerClass = 'test-table-thead-options-bar-search-bar-cancel-container-custom-class';
        $itemClass = 'test-table-thead-options-bar-search-bar-cancel-item-custom-class';
        $itemIcon = 'test-table-thead-options-bar-search-bar-cancel-item-custom-icon';
        config()->set('tablelist.template.table.thead.options-bar.search-bar.cancel.container.class', $containerClass);
        config()->set('tablelist.template.table.thead.options-bar.search-bar.cancel.item.class', $itemClass);
        config()->set('tablelist.template.table.thead.options-bar.search-bar.cancel.item.icon', $itemIcon);
        $request = Request::create('test', 'GET', [
            'rowsNumber' => config('tablelist.value.rows_number'),
            'search'     => 'Kevin',
            'sortBy'     => 'name',
            'sortDir'    => 'asc',
        ]);
        $html = $this->generateTableList($request);
        $this->assertContains($containerClass, $html);
        $this->assertEquals(1, substr_count($html, $containerClass));
        $this->assertContains($itemClass, $html);
        $this->assertEquals(1, substr_count($html, $itemClass));
        $this->assertContains($itemIcon, $html);
        $this->assertEquals(1, substr_count($html, $itemIcon));
    }

    public function testCustomTemplateTableTheadTitlesBarConfig()
    {
        $trClass = 'test-table-thead-titles-bar-tr-custom-class';
        $thClass = 'test-table-thead-titles-bar-th-custom-class';
        config()->set('tablelist.template.table.thead.titles-bar.tr.class', $trClass);
        config()->set('tablelist.template.table.thead.titles-bar.th.class', $thClass);
        $html = $this->generateTableList();
        $this->assertContains($trClass, $html);
        $this->assertEquals(1, substr_count($html, $trClass));
        $this->assertContains($thClass, $html);
        $this->assertEquals(3, substr_count($html, $thClass));
    }

    public function testCustomTemplateTableTheadTitlesBarSortConfig()
    {
        $itemClass = 'test-table-thead-titles-bar-sort-item-custom-class';
        config()->set('tablelist.template.table.thead.titles-bar.sort.item.class', $itemClass);
        $html = $this->generateTableList();
        $this->assertContains($itemClass, $html);
        $this->assertEquals(2, substr_count($html, $itemClass));
    }

    public function testCustomTemplateTableTheadTitlesBarSortAscConfig()
    {
        $itemClass = 'test-table-thead-titles-bar-sort-asc-item-custom-class';
        $itemIcon = 'test-table-thead-titles-bar-sort-asc-item-custom-icon';
        config()->set('tablelist.template.table.thead.titles-bar.sort.asc.item.class', $itemClass);
        config()->set('tablelist.template.table.thead.titles-bar.sort.asc.item.icon', $itemIcon);
        $html = $this->generateTableList();
        $this->assertContains($itemClass, $html);
        $this->assertEquals(1, substr_count($html, $itemClass));
        $this->assertContains($itemIcon, $html);
        $this->assertEquals(1, substr_count($html, $itemIcon));
    }

    public function testCustomTemplateTableTheadTitlesBarSortDescConfig()
    {
        $itemClass = 'test-table-thead-titles-bar-sort-desc-item-custom-class';
        $itemIcon = 'test-table-thead-titles-bar-sort-desc-item-custom-icon';
        config()->set('tablelist.template.table.thead.titles-bar.sort.desc.item.class', $itemClass);
        config()->set('tablelist.template.table.thead.titles-bar.sort.desc.item.icon', $itemIcon);
        $request = Request::create('test', 'GET', [
            'rowsNumber' => config('tablelist.value.rows_number'),
            'search'     => 'Kevin',
            'sortBy'     => 'name',
            'sortDir'    => 'desc',
        ]);
        $html = $this->generateTableList($request);
        $this->assertContains($itemClass, $html);
        $this->assertEquals(1, substr_count($html, $itemClass));
        $this->assertContains($itemIcon, $html);
        $this->assertEquals(1, substr_count($html, $itemIcon));
    }

    public function testCustomTemplateTableTheadTitlesBarUnsortedAscConfig()
    {
        $itemClass = 'test-table-thead-titles-bar-sort-unsorted-item-custom-class';
        $itemIcon = 'test-table-thead-titles-bar-sort-unsorted-item-custom-icon';
        config()->set('tablelist.template.table.thead.titles-bar.sort.unsorted.item.class', $itemClass);
        config()->set('tablelist.template.table.thead.titles-bar.sort.unsorted.item.icon', $itemIcon);
        $html = $this->generateTableList();
        $this->assertContains($itemClass, $html);
        $this->assertEquals(1, substr_count($html, $itemClass));
        $this->assertContains($itemIcon, $html);
        $this->assertEquals(1, substr_count($html, $itemIcon));
    }

    public function testCustomTemplateTableTbodyConfig()
    {
        $trClass = 'test-table-tbody-tr-custom-class';
        $tdClass = 'test-table-tbody-td-custom-class';
        config()->set('tablelist.template.table.tbody.tr.class', $trClass);
        config()->set('tablelist.template.table.tbody.td.class', $tdClass);
        $html = $this->generateTableList();
        $this->assertContains($trClass, $html);
        $this->assertEquals(3, substr_count($html, $trClass));
        $this->assertContains($tdClass, $html);
        $this->assertEquals(9, substr_count($html, $tdClass));
    }

    public function testCustomTemplateTableTbodyEditConfig()
    {
        $containerClass = 'test-table-tbody-edit-container-custom-class';
        $itemClass = 'test-table-tbody-edit-item-custom-class';
        $itemIcon = 'test-table-tbody-edit-item-custom-icon';
        config()->set('tablelist.template.table.tbody.edit.container.class', $containerClass);
        config()->set('tablelist.template.table.tbody.edit.item.class', $itemClass);
        config()->set('tablelist.template.table.tbody.edit.item.icon', $itemIcon);
        $html = $this->generateTableList();
        $this->assertContains($containerClass, $html);
        $this->assertEquals(3, substr_count($html, $containerClass));
        $this->assertContains($itemClass, $html);
        $this->assertEquals(3, substr_count($html, $itemClass));
        $this->assertContains($itemIcon, $html);
        $this->assertEquals(3, substr_count($html, $itemIcon));
    }

    public function testCustomTemplateTableTbodyDestroyConfig()
    {
        $containerClass = 'test-table-tbody-destroy-container-custom-class';
        $itemClass = 'test-table-tbody-destroy-item-custom-class';
        $itemIcon = 'test-table-tbody-destroy-item-custom-icon';
        config()->set('tablelist.template.table.tbody.destroy.container.class', $containerClass);
        config()->set('tablelist.template.table.tbody.destroy.item.class', $itemClass);
        config()->set('tablelist.template.table.tbody.destroy.item.icon', $itemIcon);
        $html = $this->generateTableList();
        $this->assertContains($containerClass, $html);
        $this->assertEquals(3, substr_count($html, $containerClass));
        $this->assertContains($itemClass, $html);
        $this->assertEquals(3, substr_count($html, $itemClass));
        $this->assertContains($itemIcon, $html);
        $this->assertEquals(3, substr_count($html, $itemIcon));
    }

    public function testCustomTemplateTableTbodyDestroyEnableBootstrapModalConfig()
    {
        config()->set('tablelist.template.table.tbody.destroy.trigger-bootstrap-modal', true);
        $html = $this->generateTableList();
        $this->assertContains('<div id="destroy-confirm-modal', $html);
        $this->assertEquals(3, substr_count($html, '<div id="destroy-confirm-modal'));
    }

    public function testCustomTemplateTableTbodyDestroyDisableBootstrapModalConfig()
    {
        config()->set('tablelist.template.table.tbody.destroy.trigger-bootstrap-modal', false);
        $html = $this->generateTableList();
        $this->assertNotContains('<div id="destroy-confirm-modal', $html);
        $this->assertEquals(0, substr_count($html, '<div id="destroy-confirm-modal'));
    }

    public function testCustomTemplateTableTfootConfig()
    {
        $itemClass = 'test-table-tfoot-item-custom-class';
        $trClass = 'test-table-tfoot-tr-custom-class';
        $tdClass = 'test-table-tfoot-td-custom-class';
        config()->set('tablelist.template.table.tfoot.item.class', $itemClass);
        config()->set('tablelist.template.table.tfoot.tr.class', $trClass);
        config()->set('tablelist.template.table.tfoot.td.class', $tdClass);
        $html = $this->generateTableList();
        $this->assertContains($itemClass, $html);
        $this->assertEquals(1, substr_count($html, $itemClass));
        $this->assertContains($trClass, $html);
        $this->assertEquals(1, substr_count($html, $trClass));
        $this->assertContains($tdClass, $html);
        $this->assertEquals(1, substr_count($html, $tdClass));
    }

    public function testCustomTemplateTableTfootOptionsBarConfig()
    {
        $itemClass = 'test-table-tfoot-options-bar-item-custom-class';
        config()->set('tablelist.template.table.tfoot.options-bar.item.class', $itemClass);
        $html = $this->generateTableList();
        $this->assertContains($itemClass, $html);
        $this->assertEquals(1, substr_count($html, $itemClass));
    }

    public function testCustomTemplateTableTfootOptionsBarCreateConfig()
    {
        $containerClass = 'test-table-tfoot-options-bar-create-container-custom-class';
        $itemClass = 'test-table-tfoot-options-bar-create-item-custom-class';
        $itemIcon = 'test-table-tfoot-options-bar-create-item-custom-icon';
        config()->set('tablelist.template.table.tfoot.options-bar.create.container.class', $containerClass);
        config()->set('tablelist.template.table.tfoot.options-bar.create.item.class', $itemClass);
        config()->set('tablelist.template.table.tfoot.options-bar.create.item.icon', $itemIcon);
        $html = $this->generateTableList();
        $this->assertContains($containerClass, $html);
        $this->assertEquals(1, substr_count($html, $containerClass));
        $this->assertContains($itemClass, $html);
        $this->assertEquals(1, substr_count($html, $itemClass));
        $this->assertContains($itemIcon, $html);
        $this->assertEquals(1, substr_count($html, $itemIcon));
    }

    public function testCustomTemplateTableTfootOptionsBarNavigationWithCreateRouteConfig()
    {
        $containerClass = 'test-table-tfoot-options-bar-navigation-container-custom-class';
        config()->set(
            'tablelist.template.table.tfoot.options-bar.navigation.with-create-route.container.class',
            $containerClass
        );
        $html = $this->generateTableList();
        $this->assertContains($containerClass, $html);
        $this->assertEquals(1, substr_count($html, $containerClass));
    }

    public function testCustomTemplateTableTfootOptionsBarNavigationWithoutCreateRouteConfig()
    {
        $containerClass = 'test-table-tfoot-options-bar-navigation-container-custom-class';
        config()->set(
            'tablelist.template.table.tfoot.options-bar.navigation.without-create-route.container.class',
            $containerClass
        );
        $html = $this->generateTableList(null, [
            'index'   => ['alias' => 'users.index', 'parameters' => []],
            'edit'    => ['alias' => 'users.edit', 'parameters' => []],
            'destroy' => ['alias' => 'users.destroy', 'parameters' => []],
        ]);
        $this->assertContains($containerClass, $html);
        $this->assertEquals(1, substr_count($html, $containerClass));
    }

    public function testCustomTemplateTableTfootOptionsBarPaginationWithCreateRouteConfig()
    {
        $containerClass = 'test-table-tfoot-options-bar-pagination-container-custom-class';
        config()->set(
            'tablelist.template.table.tfoot.options-bar.pagination.with-create-route.container.class',
            $containerClass
        );
        $html = $this->generateTableList();
        $this->assertContains($containerClass, $html);
        $this->assertEquals(1, substr_count($html, $containerClass));
    }

    public function testCustomTemplateTableTfootOptionsBarPaginationWithoutCreateRouteConfig()
    {
        $containerClass = 'test-table-tfoot-options-bar-pagination-container-custom-class';
        config()->set(
            'tablelist.template.table.tfoot.options-bar.pagination.without-create-route.container.class',
            $containerClass
        );
        $html = $this->generateTableList(null, [
            'index'   => ['alias' => 'users.index', 'parameters' => []],
            'edit'    => ['alias' => 'users.edit', 'parameters' => []],
            'destroy' => ['alias' => 'users.destroy', 'parameters' => []],
        ]);
        $this->assertContains($containerClass, $html);
        $this->assertEquals(1, substr_count($html, $containerClass));
    }

    public function testCustomTemplateModalConfig()
    {
        config()->set('tablelist.template.table.tbody.destroy.trigger-bootstrap-modal', true);
        $containerClass = 'test-modal-container-custom-class';
        $itemClass = 'test-modal-item-custom-class';
        config()->set('tablelist.template.modal.container.class', $containerClass);
        config()->set('tablelist.template.modal.item.class', $itemClass);
        $html = $this->generateTableList();
        $this->assertContains($containerClass, $html);
        $this->assertEquals(3, substr_count($html, $containerClass));
        $this->assertContains($itemClass, $html);
        $this->assertEquals(3, substr_count($html, $itemClass));
    }

    public function testCustomTemplateModalTitleConfig()
    {
        config()->set('tablelist.template.table.tbody.destroy.trigger-bootstrap-modal', true);
        $containerClass = 'test-modal-title-container-custom-class';
        $itemClass = 'test-modal-title-item-custom-class';
        config()->set('tablelist.template.modal.title.container.class', $containerClass);
        config()->set('tablelist.template.modal.title.item.class', $itemClass);
        $html = $this->generateTableList();
        $this->assertContains($containerClass, $html);
        $this->assertEquals(3, substr_count($html, $containerClass));
        $this->assertContains($itemClass, $html);
        $this->assertEquals(3, substr_count($html, $itemClass));
    }

    public function testCustomTemplateModalBodyConfig()
    {
        config()->set('tablelist.template.table.tbody.destroy.trigger-bootstrap-modal', true);
        $itemClass = 'test-modal-body-item-custom-class';
        config()->set('tablelist.template.modal.body.item.class', $itemClass);
        $html = $this->generateTableList();
        $this->assertContains($itemClass, $html);
        $this->assertEquals(3, substr_count($html, $itemClass));
    }

    public function testCustomTemplateModalFooterConfig()
    {
        config()->set('tablelist.template.table.tbody.destroy.trigger-bootstrap-modal', true);
        $itemClass = 'test-modal-footer-item-custom-class';
        config()->set('tablelist.template.modal.footer.item.class', $itemClass);
        $html = $this->generateTableList();
        $this->assertContains($itemClass, $html);
        $this->assertEquals(3, substr_count($html, $itemClass));
    }

    public function testCustomTemplateModalFooterConfirmConfig()
    {
        config()->set('tablelist.template.table.tbody.destroy.trigger-bootstrap-modal', true);
        $itemClass = 'test-modal-footer-confirm-item-custom-class';
        $itemIcon = 'test-modal-footer-confirm-item-custom-icon';
        config()->set('tablelist.template.modal.footer.confirm.item.class', $itemClass);
        config()->set('tablelist.template.modal.footer.confirm.item.icon', $itemIcon);
        $html = $this->generateTableList();
        $this->assertContains($itemClass, $html);
        $this->assertEquals(3, substr_count($html, $itemClass));
        $this->assertContains($itemIcon, $html);
        $this->assertEquals(3, substr_count($html, $itemIcon));
    }

    public function testCustomTemplateModalFooterCancelConfig()
    {
        config()->set('tablelist.template.table.tbody.destroy.trigger-bootstrap-modal', true);
        $itemClass = 'test-modal-footer-cancel-item-custom-class';
        $itemIcon = 'test-modal-footer-cancel-item-custom-icon';
        config()->set('tablelist.template.modal.footer.cancel.item.class', $itemClass);
        config()->set('tablelist.template.modal.footer.cancel.item.icon', $itemIcon);
        $html = $this->generateTableList();
        $this->assertContains($itemClass, $html);
        $this->assertEquals(3, substr_count($html, $itemClass));
        $this->assertContains($itemIcon, $html);
        $this->assertEquals(3, substr_count($html, $itemIcon));
    }
}
