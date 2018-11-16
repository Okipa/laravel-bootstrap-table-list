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
    protected function checkModelIsDefined(): void
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
    protected function checkColumnsValidity(): void
    {
        $this->getAttribute('columns')->map(function (TableListColumn $column) {
            $this->checkSortableColumnHasAttribute($column);
            $isSearchable = in_array(
                $column->getAttribute('attribute'),
                $this->getAttribute('searchableColumns')->pluck('attribute')->toArray()
            );
            if ($isSearchable) {
                $this->checkSearchableColumnHasAttribute($column);
                $this->checkSearchedAttributeDoesExistInRelatedTable($column);
            }
        });
    }

    /**
     * Check if the sortable column has an attribute.
     *
     * @param \Okipa\LaravelBootstrapTableList\TableListColumn $column
     *
     * @return void
     * @throws \ErrorException
     */
    protected function checkSortableColumnHasAttribute(TableListColumn $column): void
    {
        if (! $column->getAttribute('attribute') && $column->getAttribute('isSortableColumn')) {
            $errorMessage = 'One of the sortable columns has no defined attribute. You have to define a column '
                            . 'attribute for each sortable columns by setting a string parameter in the '
                            . '« addColumn() » method.';
            throw new ErrorException($errorMessage);
        }
    }

    /**
     * Check if the searchable column has an attribute.
     *
     * @param \Okipa\LaravelBootstrapTableList\TableListColumn $column
     *
     * @return void
     * @throws \ErrorException
     */
    protected function checkSearchableColumnHasAttribute(TableListColumn $column): void
    {
        if (! $column->getAttribute('attribute')) {
            $errorMessage = 'One of the searchable columns has no defined attribute. You have to define a column '
                            . 'attribute for each searchable columns by setting a string parameter in the '
                            . '« addColumn() » method.';
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
    protected function checkSearchedAttributeDoesExistInRelatedTable(TableListColumn $column): void
    {
        $attribute = $column->getAttribute('customColumnTableRealAttribute')
            ? $column->getAttribute('customColumnTableRealAttribute')
            : $column->getAttribute('attribute');
        $tableColumns = Schema::getColumnListing($column->getAttribute('customColumnTable'));
        if (! in_array($attribute, $tableColumns)) {
            $errorMessage = 'The given searchable column attribute « ' . $attribute . ' » does not exist in the « '
                            . $column->getAttribute('customColumnTable')
                            . ' » table. Set the correct column-related table and the associated real attribute '
                            . '(if necessary) with the « setCustomTable() » method.';
            throw new ErrorException($errorMessage);
        }
    }

    /**
     * Check if at least one column is declared.
     *
     * @return void
     * @throws \ErrorException
     */
    protected function checkIfAtLeastOneColumnIsDeclared(): void
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
    protected function checkDestroyAttributesDefinition(): void
    {
        if ($this->isRouteDefined('destroy') && $this->getAttribute('destroyAttributes')->isEmpty()) {
            $errorMessage = 'No columns have been chosen for the destroy confirmation. '
                            . 'Use the « useForDestroyConfirmation() » method on column objects to define them.';
            throw new ErrorException($errorMessage);
        }
    }
}
