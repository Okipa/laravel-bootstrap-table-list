<?php

namespace Okipa\LaravelBootstrapTableList;

use Closure;
use ErrorException;
use Illuminate\Database\Eloquent\Model;
use InvalidArgumentException;

class TableListColumn extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'tableList',
        'customColumnTable',
        'customColumnTableRealAttribute',
        'attribute',
        'isSortableColumn',
        'title',
        'columnDateFormat',
        'columnDateTimeFormat',
        'buttonClass',
        'stringLimit',
        'url',
        'customValueClosure',
        'customHtmlEltClosure',
        'icon',
        'showIconWithNoValue',
    ];

    /**
     * TableListColumn constructor.
     *
     * @param TableList $tableList
     * @param string|null $attribute
     */
    public function __construct(TableList $tableList, string $attribute = null)
    {
        parent::__construct([
            'tableList'         => $tableList,
            'customColumnTable' => $tableList->getAttribute('tableModel')->getTable(),
            'attribute'         => $attribute,
            'title'             => $attribute ? trans('validation.attributes.' . $attribute) : null,
        ]);
    }

    /**
     * Set the column title (optional).
     *
     * @param string|null $title
     *
     * @return \Okipa\LaravelBootstrapTableList\TableListColumn
     */
    public function setTitle(string $title = null): TableListColumn
    {
        $this->setAttribute('title', $title);

        return $this;
    }

    /**
     * Set the default sorted column (required).
     *
     * @param string $direction (default: "asc", accepts "asc" or "desc")
     *
     * @return \Okipa\LaravelBootstrapTableList\TableListColumn
     * @throws ErrorException
     */
    public function sortByDefault(string $direction = 'asc'): TableListColumn
    {
        // we check if the method has already been called
        if ($this->getAttribute('tableList')->sortBy || $this->getAttribute('tableList')->sortDir) {
            $errorMessage = 'The « sortByDefault() » method has already been called. '
                            . 'You only can sort a column by default once.';
            throw new ErrorException($errorMessage);
        }
        // we set the sort attribute
        $this->getAttribute('tableList')->sortBy = $this->getAttribute('attribute');
        // we set the sort direction
        $acceptedDirections = ['asc', 'desc'];
        $errorMessage = 'Invalid « $direction » argument for « sortByAttribute() » method. Has to be « asc » or '
                        . '« desc ». « ' . $direction . ' » given.';
        if (! in_array($direction, $acceptedDirections)) {
            throw new InvalidArgumentException($errorMessage);
        }
        $this->getAttribute('tableList')->setAttribute('sortDir', $direction);

        return $this;
    }

    /**
     * Use the column attribute for the destroy confirmation message generation (required).
     * This method can be called only once.
     *
     * @return \Okipa\LaravelBootstrapTableList\TableListColumn
     * @throws ErrorException
     */
    public function useForDestroyConfirmation(): TableListColumn
    {
        if (! $this->getAttribute('attribute')) {
            $errorMessage = 'You cannot use the « useForDestroyConfirmation() » method on a column which have no '
                            . 'defined attribute. Define a column attribute by setting '
                            . 'a string parameter in the « addColumn() » method.';
            throw new ErrorException($errorMessage);
        }
        $this->getAttribute('tableList')->getAttribute('destroyAttributes')->add($this->getAttribute('attribute'));

        return $this;
    }

    /**
     * Make the column sortable (optional).
     *
     * @return \Okipa\LaravelBootstrapTableList\TableListColumn
     */
    public function isSortable(): TableListColumn
    {
        $this->getAttribute('tableList')->getAttribute('sortableColumns')->add($this);
        $this->setAttribute('isSortableColumn', true);

        return $this;
    }

    /**
     * Make the column searchable (optional).
     *
     * @return \Okipa\LaravelBootstrapTableList\TableListColumn
     */
    public function isSearchable(): TableListColumn
    {
        $this->getAttribute('tableList')->getAttribute('searchableColumns')->add($this);

        return $this;
    }

    /**
     * Set a custom related table for the column and a facultative alias for the column attribute (optional).
     * Calling this method is mandatory if you define your column as searchable and if the column attribute does not
     * directly belong to the table list model.
     *
     * @param string $customColumnTable
     * @param string|null $customColumnTableRealAttribute
     *
     * @return \Okipa\LaravelBootstrapTableList\TableListColumn
     */
    public function setCustomTable(
        string $customColumnTable,
        string $customColumnTableRealAttribute = null
    ): TableListColumn {
        $this->setAttribute('customColumnTable', $customColumnTable);
        $this->setAttribute('customColumnTableRealAttribute', $customColumnTableRealAttribute);

        return $this;
    }

    /**
     * Set the format for a date (optional).
     * (Carbon is used to format the date).
     *
     * @deprecated 2.0 This method will be removed in the v2.0. Please use the setColumnDateTimeFormat() method instead.
     *
     * @param string $columnDateFormat
     *
     * @return \Okipa\LaravelBootstrapTableList\TableListColumn
     */
    public function setColumnDateFormat(string $columnDateFormat): TableListColumn
    {
        $this->setAttribute('columnDateFormat', $columnDateFormat);

        return $this;
    }

    /**
     * Set the format for a datetime, date or time attribute (optional).
     * (Carbon::parse($value)->format($format) method is used under the hood).
     *
     * @param string $columnDateTimeFormat
     *
     * @return \Okipa\LaravelBootstrapTableList\TableListColumn
     */
    public function setColumnDateTimeFormat(string $columnDateTimeFormat): TableListColumn
    {
        $this->setAttribute('columnDateTimeFormat', $columnDateTimeFormat);

        return $this;
    }

    /**
     * Set the column button class (optional).
     * The attribute is wrapped into a button.
     *
     * @param array $buttonClass
     *
     * @return \Okipa\LaravelBootstrapTableList\TableListColumn
     */
    public function isButton(array $buttonClass = []): TableListColumn
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
     * @return \Okipa\LaravelBootstrapTableList\TableListColumn
     */
    public function setStringLimit(int $stringLimit): TableListColumn
    {
        $this->setAttribute('stringLimit', $stringLimit);

        return $this;
    }

    /**
     * Set the icon to display before the value (optional).
     *
     * @param string $icon
     * @param bool $showWithNoValue
     *
     * @return \Okipa\LaravelBootstrapTableList\TableListColumn
     */
    public function setIcon(string $icon, $showWithNoValue = false): TableListColumn
    {
        $this->setAttribute('icon', $icon);
        $this->setAttribute('showIconWithNoValue', $showWithNoValue);

        return $this;
    }

    /**
     * Set the link url.
     * You can declare the link as a string or as a closure which will let you manipulate the following attributes :
     * $entity, $column.
     * If no url is declared, it will be set with the column value.
     *
     * @param string|Closure|null $url
     *
     * @return \Okipa\LaravelBootstrapTableList\TableListColumn
     */
    public function isLink($url = null): TableListColumn
    {
        $this->setAttribute('url', $url ?: true);

        return $this;
    }

    /**
     * Set a custom value in the method closure (optional).
     * The closure let you manipulate the following attributes : $entity, $column.
     *
     * @param Closure $customValueClosure
     *
     * @return \Okipa\LaravelBootstrapTableList\TableListColumn
     */
    public function isCustomValue(Closure $customValueClosure): TableListColumn
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
     * @return \Okipa\LaravelBootstrapTableList\TableListColumn
     */
    public function isCustomHtmlElement(Closure $customHtmlEltClosure): TableListColumn
    {
        $this->setAttribute('customHtmlEltClosure', $customHtmlEltClosure);

        return $this;
    }
}
