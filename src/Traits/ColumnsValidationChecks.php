<?php

namespace Okipa\LaravelBootstrapTableList\Traits;

use ErrorException;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Schema;
use Okipa\LaravelBootstrapTableList\TableListColumn;

trait ColumnsValidationChecks
{
    /**
     * Get an attribute from the model.
     *
     * @param  string $key
     *
     * @return mixed
     */
    public abstract function getAttribute($key);

    /**
     * Check if a route is defined from its key.
     *
     * @param string $routeKey
     *
     * @return bool
     */
    public abstract function isRouteDefined(string $routeKey): bool;
    
    /**
     * Check column model is defined.
     *
     * @return void
     * @throws \ErrorException
     */
    private function checkModelIsDefined(): void
    {
        if (! $this->getAttribute('tableModel') instanceof Model) {
            $errorMessage = 'The table list model has not been defined or is not an instance of « '
                            . Model::class . ' ».';
            throw new ErrorException($errorMessage);
        }
    }

    /**
     * Check the columns validity.
     *
     * @return void
     */
    private function checkColumnsValidity(): void
    {
        $this->getAttribute('columns')->map(function(TableListColumn $column) {
            $this->checkColumnAttributeIsDeclaredWithAlias($column);
            $this->checkSortableColumnHasAttribute($column);
            $this->checkAttributeAttributeOrAliasFieldDoesExistRelatedTable($column);
        });
    }

    /**
     * Check if a column attribute is defined when an alias is found.
     *
     * @param \Okipa\LaravelBootstrapTableList\TableListColumn $column
     *
     * @return void
     * @throws \ErrorException
     */
    private function checkColumnAttributeIsDeclaredWithAlias(TableListColumn $column): void
    {
        if ($column->getAttribute('columnDatabaseAlias') && ! $column->getAttribute('attribute')) {
            $errorMessage = 'You must define an attribute when declaring an alias with the « setCustomTable() » method.'
                            . ' No attribute detected for the column aliased column « '
                            . $column->getAttribute('columnDatabaseAlias') . ' ».';
            throw new ErrorException($errorMessage);
        }
    }

    /**
     * Check that the column has an attribute if it is a sortable column.
     *
     * @param \Okipa\LaravelBootstrapTableList\TableListColumn $column
     *
     * @return void
     * @throws \ErrorException
     */
    private function checkSortableColumnHasAttribute(TableListColumn $column): void
    {
        if (! $column->getAttribute('attribute') && $column->getAttribute('isSortableColumn')) {
            $errorMessage = 'A sortable column has no defined attribute. Define a column attribute for each sortable '
                            . 'columns by setting a string parameter in the « addColumn() » method.';
            throw new ErrorException($errorMessage);
        }
    }

    /**
     * Check that the given attribute or alias exist in the column related table.
     *
     * @param \Okipa\LaravelBootstrapTableList\TableListColumn $column
     *
     * @return void
     * @throws \ErrorException
     */
    private function checkAttributeAttributeOrAliasFieldDoesExistRelatedTable(TableListColumn $column): void
    {
        $attribute = $column->getAttribute('columnDatabaseAlias')
            ? $column->getAttribute('columnDatabaseAlias')
            : $column->getAttribute('attribute');
        $tableColumns = Schema::getColumnListing($column->getAttribute('customColumnTable'));
        $isSearchable = in_array(
            $column->getAttribute('attribute'),
            $this->getAttribute('searchableColumns')->pluck('attribute')->toArray()
        );
        if ($attribute && ! in_array($attribute, $tableColumns) && $isSearchable) {
            $errorMessage = 'The given ' . ($isSearchable ? 'searchable' : '')
                            . ' column ' . (
                            $column->getAttribute('columnDatabaseAlias')
                                ? 'database alias'
                                : 'attribute'
                            ) . ' « ' . $attribute . ' » does not exist in the « '
                            . $column->getAttribute('customColumnTable')
                            . ' » table. Set the correct column-related table and alias with the '
                            . '« setCustomTable() » method.';
            throw new ErrorException($errorMessage);
        }
    }

    /**
     * Check if at least one column is declared.
     *
     * @return void
     * @throws \ErrorException
     */
    private function checkIfAtLeastOneColumnIsDeclared(): void
    {
        if ($this->getAttribute('columns')->isEmpty()) {
            $errorMessage = 'No column has been added to the table list. Please add at least one column by using the '
                            . '« addColumn() » method on the table list object.';
            throw new ErrorException($errorMessage);
        }
    }

    /**
     * Check that a destroy attribute has been defined.
     *
     * @return void
     * @throws \ErrorException
     */
    private function checkDestroyAttributesDefinition(): void
    {
        if ($this->isRouteDefined('destroy') && $this->getAttribute('destroyAttributes')->isEmpty()) {
            $errorMessage = 'No columns have been chosen for the destroy confirmation. '
                            . 'Use the « useForDestroyConfirmation() » method on column objects to define them.';
            throw new ErrorException($errorMessage);
        }
    }
}
