<?php

namespace Okipa\LaravelBootstrapTableList;

use Closure;
use ErrorException;
use Illuminate\Database\Eloquent\Model;
use InvalidArgumentException;

class TableListColumn extends Model
{
    protected $fillable = [
        'tableList',
        'customColumnTable',
        'attribute',
        'isSortableColumn',
        'title',
        'columnDateFormat',
        'buttonClass',
        'stringLimit',
        'linkClosure',
        'customValueClosure',
        'customHtmlEltClosure',
    ];

    /**
     * TableListColumn constructor.
     *
     * @param TableList   $tableList
     * @param string|null $attribute
     */
    public function __construct(TableList $tableList, string $attribute = null)
    {
        parent::__construct([
            'tableList'         => $tableList,
            'customColumnTable' => $tableList->tableModel->getTable(),
            'attribute'         => $attribute,
        ]);
    }

    /**
     * Set the column title (required).
     *
     * @param string|null $title
     *
     * @return TableListColumn
     */
    public function setTitle(string $title = null)
    {
        $this->setAttribute('title', $title);

        return $this;
    }

    /**
     * Set the default sorted column (required).
     *
     * @param string $direction (default: "asc", accepts "asc" or "desc")
     *
     * @return $this
     * @throws ErrorException
     */
    public function sortByDefault(string $direction = 'asc')
    {
        // we check if the method has already been called
        if ($this->tableList->sortBy || $this->tableList->sortDir) {
            $errorMessage = 'The sortByDefault() method has already been called. '
                            . 'You can sort a column by default only once.';
            throw new ErrorException($errorMessage);
        }
        // we set the sort attribute
        $this->tableList->sortBy = $this->attribute;
        // we set the sort direction
        $acceptedDirections = ['asc', 'desc'];
        $errorMessage = 'Invalid $direction argument for sortByAttribute() method. Has to be "asc" or "desc". "'
                        . $direction . '" given.';
        if (!in_array($direction, $acceptedDirections))
            throw new InvalidArgumentException($errorMessage);
        $this->tableList->setAttribute('sortDir', $direction);

        return $this;
    }

    /**
     * Use the column attribute for the destroy confirmation message generation (required).
     *
     * @return $this
     * @throws ErrorException
     */
    public function useForDestroyConfirmation()
    {
        if ($this->tableList->destroyAttribute) {
            $errorMessage = 'The useForDestroyConfirmation() method has already been called. '
                            . 'You can define a column attribute for the destroy confirmation only once.';
            throw new ErrorException($errorMessage);
        }
        $this->tableList->setAttribute('destroyAttribute', $this->attribute);

        return $this;
    }

    /**
     * Make the column sortable (optional).
     *
     * @return $this
     */
    public function isSortable()
    {
        $this->tableList->sortableColumns->add($this);
        $this->setAttribute('isSortableColumn', true);

        return $this;
    }

    /**
     * Make the column searchable (optional).
     *
     * @return TableListColumn
     */
    public function isSearchable()
    {
        $this->tableList->searchableColumns->add($this);

        return $this;
    }

    /**
     * Set a custom table for the column (optional).
     * Calling this method can be useful if the column attribute does not directly belong to the table list model.
     *
     * @param string $customColumnTable
     *
     * @return TableListColumn
     */
    public function setCustomTable(string $customColumnTable)
    {
        $this->setAttribute('customColumnTable', $customColumnTable);

        return $this;
    }

    /**
     * Set the format for a date (optional).
     * (Carbon is used for formatting the date).
     *
     * @param string|null $columnDateFormat
     *
     * @return $this|string
     */
    public function setColumnDateFormat(string $columnDateFormat)
    {
        $this->setAttribute('columnDateFormat', $columnDateFormat);

        return $this;
    }

    /**
     * Set the column button class (optional).
     * The attribute is wrapped into a button.
     *
     * @param string $buttonClass
     *
     * @return TableListColumn
     */
    public function isButton(string $buttonClass)
    {
        $this->setAttribute('buttonClass', $buttonClass);

        return $this;
    }

    /**
     * Set the string value display limitation (optional).
     * Shows "..." when the limit is reached.
     *
     * @param int $stringLimit
     *
     * @return $this
     */
    public function setStringLimit(int $stringLimit)
    {
        $this->setAttribute('stringLimit', $stringLimit);

        return $this;
    }

    /**
     * Set the link in the method closure (optional).
     * The closure let you manipulate the following attributes : $entity, $column.
     *
     * @param Closure $linkClosure
     *
     * @return $this
     */
    public function isLink(Closure $linkClosure)
    {
        $this->setAttribute('linkClosure', $linkClosure);

        return $this;
    }

    /**
     * Set a custom value in the method closure (optional).
     * The closure let you manipulate the following attributes : $entity, $column.
     *
     * @param Closure $customValueClosure
     *
     * @return $this
     */
    public function isCustomValue(Closure $customValueClosure)
    {
        $this->setAttribute('customValueClosure', $customValueClosure);

        return $this;
    }

    /**
     * Set the HTML element to render in the method closure (optional).
     * The closure let you manipulate the following attributes : $entity, $column.
     *
     * @param Closure $customHtmlEltClosure
     *
     * @return $this
     */
    public function isCustomHtmlElement(Closure $customHtmlEltClosure)
    {
        $this->setAttribute('customHtmlEltClosure', $customHtmlEltClosure);

        return $this;
    }
}
