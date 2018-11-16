<?php

namespace Okipa\LaravelBootstrapTableList;

use Closure;
use ErrorException;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Okipa\LaravelBootstrapTableList\Traits\ColumnsValidationChecks;
use Okipa\LaravelBootstrapTableList\Traits\RoutesValidationChecks;

class TableList extends Model implements Htmlable
{
    use RoutesValidationChecks;
    use ColumnsValidationChecks;
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'tableModel',
        'rowsNumber',
        'rowsNumberSelectorEnabled',
        'sortableColumns',
        'sortBy',
        'sortDir',
        'searchableColumns',
        'request',
        'routes',
        'columns',
        'queryClosure',
        'disableLinesClosure',
        'disableLinesClass',
        'highlightLinesClosure',
        'list',
        'destroyAttributes',
    ];

    /**
     * TableList constructor.
     */
    public function __construct()
    {
        parent::__construct([
            'rowsNumber'        => config('tablelist.value.rows_number'),
            'sortableColumns'   => new Collection(),
            'searchableColumns' => new Collection(),
            'request'           => request(),
            'routes'            => [],
            'columns'           => new Collection(),
            'destroyAttributes' => new Collection(),
        ]);
    }

    /**
     * Set the model used for the table list generation (required).
     *
     * @param string $tableModel
     *
     * @return \Okipa\LaravelBootstrapTableList\TableList
     */
    public function setModel(string $tableModel): TableList
    {
        $this->setAttribute('tableModel', app()->make($tableModel));

        return $this;
    }

    /**
     * Set the request used for the table list generation (required).
     *
     * @param Request $request
     *
     * @return \Okipa\LaravelBootstrapTableList\TableList
     */
    public function setRequest(Request $request): TableList
    {
        $this->setAttribute('request', $request);

        return $this;
    }

    /**
     * Set the routes used for the table list generation (required).
     *
     * @param array $routes
     *
     * @return \Okipa\LaravelBootstrapTableList\TableList
     * @throws \ErrorException
     */
    public function setRoutes(array $routes): TableList
    {
        $this->checkRoutesValidity($routes);
        $this->setAttribute('routes', $routes);

        return $this;
    }

    /**
     * Set a custom number of rows for the table list (optional).
     *
     * @param int $rowsNumber
     *
     * @return \Okipa\LaravelBootstrapTableList\TableList
     */
    public function setRowsNumber(int $rowsNumber): TableList
    {
        $this->setAttribute('rowsNumber', $rowsNumber);

        return $this;
    }

    /**
     * Enables the rows number selection in the table list (optional).
     *
     * @return \Okipa\LaravelBootstrapTableList\TableList
     */
    public function enableRowsNumberSelector(): TableList
    {
        $this->setAttribute('rowsNumberSelectorEnabled', true);

        return $this;
    }

    /**
     * Set the query closure that will be executed during the table list generation (optional).
     * For example, you can define your joined tables here.
     * The closure let you manipulate the following attribute : $query.
     *
     * @param Closure $queryClosure
     *
     * @return \Okipa\LaravelBootstrapTableList\TableList
     */
    public function addQueryInstructions(Closure $queryClosure): TableList
    {
        $this->setAttribute('queryClosure', $queryClosure);

        return $this;
    }

    /**
     * Set the disable lines closure that will be executed during the table list generation (optional).
     * The optional second param let you set the class that will be applied for the disabled lines.
     * By default, the « config('tablelist.value.disabled_line.class') » config value is applied.
     * For example, you can disable the current logged user to prevent him being
     * edited or deleted from the table list.
     * The closure let you manipulate the following attribute : $model.
     *
     * @param \Closure $disableLinesClosure
     * @param array $lineClass
     *
     * @return \Okipa\LaravelBootstrapTableList\TableList
     */
    public function disableLines(Closure $disableLinesClosure, array $lineClass = []): TableList
    {
        $this->setAttribute('disableLinesClosure', $disableLinesClosure);
        $this->setAttribute(
            'disableLinesClass',
            ! empty($lineClass) ? $lineClass : config('tablelist.value.disabled_line.class')
        );

        return $this;
    }

    /**
     * Set the highlight lines closure that will executed during the table list generation (optional).
     * The optional second param let you set the class that will be applied for the highlighted lines.
     * By default, the « config('tablelist.value.highlighted_line.class') » config value is applied.
     * The closure let you manipulate the following attribute : $model.
     *
     * @param \Closure $highlightLinesClosure
     * @param array $lineClass
     *
     * @return \Okipa\LaravelBootstrapTableList\TableList
     */
    public function highlightLines(Closure $highlightLinesClosure, array $lineClass = []): TableList
    {
        $this->setAttribute('highlightLinesClosure', $highlightLinesClosure);
        $this->setAttribute(
            'highlightLinesClass',
            ! empty($lineClass) ? $lineClass : config('tablelist.value.highlighted_line.class')
        );

        return $this;
    }

    /**
     * Add a column that will be displayed in the table list (required).
     *
     * @param string|null $attribute
     *
     * @return \Okipa\LaravelBootstrapTableList\TableListColumn
     * @throws ErrorException
     */
    public function addColumn(string $attribute = null): TableListColumn
    {
        $this->checkModelIsDefined();
        $column = new TableListColumn($this, $attribute);
        $this->getAttribute('columns')[] = $column;

        return $column;
    }

    /**
     * Get the searchable columns titles.
     *
     * @return string
     */
    public function getSearchableTitles(): string
    {
        return $this->getAttribute('searchableColumns')->implode('title', ', ');
    }

    /**
     * Get the columns count.
     *
     * @return int
     */
    public function getColumnsCount(): int
    {
        return count($this->getAttribute('columns'));
    }

    /**
     * Get the route from its key.
     *
     * @param string $routeKey
     * @param array $params
     *
     * @return string
     */
    public function getRoute(string $routeKey, array $params = []): string
    {
        $this->checkRouteIsDefined($routeKey);

        return route(
            $this->getAttribute('routes')[$routeKey]['alias'],
            array_merge($this->getAttribute('routes')[$routeKey]['parameters'], $params)
        );
    }

    /**
     * Get the navigation status from the table list.
     *
     * @return string
     */
    public function navigationStatus(): string
    {
        return trans('tablelist::tablelist.tfoot.nav', [
            'start' => ($this->getAttribute('list')->perPage()
                        * ($this->getAttribute('list')->currentPage() - 1))
                       + 1,
            'stop'  => $this->getAttribute('list')->count()
                       + (($this->getAttribute('list')->currentPage() - 1)
                          * $this->getAttribute('list')->perPage()),
            'total' => $this->getAttribute('list')->total(),
        ]);
    }

    /**
     * Get content as a string of HTML.
     *
     * @return string
     * @throws \ErrorException
     */
    public function toHtml(): string
    {
        return (string) $this->render();
    }

    /**
     * Generate the table list html.
     *
     * @return string
     * @throws ErrorException
     */
    public function render(): string
    {
        $this->checkRoutesValidity($this->getAttribute('routes'));
        $this->checkIfAtLeastOneColumnIsDeclared();
        $this->checkDestroyAttributesDefinition();
        $this->handleRequest();
        $this->generateEntitiesListFromQuery();

        return view('tablelist::table', ['table' => $this]);
    }

    /**
     * Handle the request treatments.
     *
     * @return void
     */
    protected function handleRequest(): void
    {
        $validator = Validator::make(
            $this->getAttribute('request')->only('rowsNumber', 'search', 'sortBy', 'sortDir'),
            [
                'rowsNumber' => 'required|numeric',
                'search'     => 'nullable|string',
                'sortBy'     => 'nullable|string|in:' . $this->getAttribute('columns')->implode('attribute', ','),
                'sortDir'    => 'nullable|string|in:asc,desc',
            ]
        );
        if ($validator->fails()) {
            $this->getAttribute('request')->merge([
                'rowsNumber' => $this->getAttribute('rowsNumber')
                    ? $this->getAttribute('rowsNumber')
                    : config('tablelist.value.rows_number'),
                'search'     => null,
                'sortBy'     => $this->getAttribute('sortBy'),
                'sortDir'    => $this->getAttribute('sortDir'),
            ]);
        } else {
            $this->setAttribute('rowsNumber', $this->getAttribute('request')->rowsNumber);
            $this->setAttribute('search', $this->getAttribute('request')->search);
        }
    }

    /**
     * Generate the entities list.
     *
     * @return void
     */
    protected function generateEntitiesListFromQuery(): void
    {
        $query = $this->getAttribute('tableModel')->query();
        $this->applyQueryClosure($query);
        $this->checkColumnsValidity();
        $this->applySearchClauses($query);
        $this->applySortClauses($query);
        $this->paginateList($query);
        $this->applyClosuresOnPaginatedList();
    }

    /**
     * Apply query closure
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     *
     * @return void
     */
    protected function applyQueryClosure(Builder $query): void
    {
        if ($closure = $this->getAttribute('queryClosure')) {
            $closure($query);
        }
    }

    /**
     * Apply search clauses
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     *
     * @return void
     */
    protected function applySearchClauses(Builder $query): void
    {
        if ($searched = $this->getAttribute('request')->search) {
            $this->getAttribute('searchableColumns')
                ->map(function (TableListColumn $column, int $key) use (&$query, $searched) {
                    $table = $column->getAttribute('customColumnTable');
                    $attribute = $column->getAttribute('customColumnTableRealAttribute')
                        ? $column->getAttribute('customColumnTableRealAttribute')
                        : $column->getAttribute('attribute');
                    if ($key > 0) {
                        $query->orWhere($table . '.' . $attribute, 'like', '%' . $searched . '%');
                    } else {
                        $query->where($table . '.' . $attribute, 'like', '%' . $searched . '%');
                    }
                });
        }
    }

    /**
     * Apply sort clauses.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     *
     * @return void
     */
    protected function applySortClauses(Builder $query): void
    {
        $sortBy = $this->getAttribute('request')->get('sortBy', $this->getAttribute('sortBy'));
        $sortDir = $this->getAttribute('request')->get('sortDir', $this->getAttribute('sortDir'));
        if ($sortBy && $sortDir) {
            $query->orderBy($sortBy, $sortDir);
            $this->setAttribute('sortBy', $sortBy);
            $this->setAttribute('sortDir', $sortDir);
        }
    }

    /**
     * Paginate the list from the query.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     *
     * @return void
     */
    protected function paginateList(Builder $query): void
    {
        $this->setAttribute('list', $query->paginate($this->getAttribute('rowsNumber')));
        $this->getAttribute('list')->appends([
            'rowsNumber' => $this->getAttribute('rowsNumber'),
            'search'     => $this->getAttribute('search'),
            'sortBy'     => $this->getAttribute('sortBy'),
            'sortDir'    => $this->getAttribute('sortDir'),
        ]);
    }

    /**
     * Apply the closures on the paginated list.
     *
     * @return void
     */
    protected function applyClosuresOnPaginatedList(): void
    {
        $disableLinesClosure = $this->getAttribute('disableLinesClosure');
        $highlightLinesClosure = $this->getAttribute('highlightLinesClosure');
        $this->getAttribute('list')->getCollection()->transform(function ($model) use (
            $disableLinesClosure,
            $highlightLinesClosure
        ) {
            if (isset($disableLinesClosure)) {
                $model->setAttribute('disabled', $disableLinesClosure($model));
            }
            if (isset($highlightLinesClosure)) {
                $model->setAttribute('highlighted', $highlightLinesClosure($model));
            }

            return $model;
        });
    }

    /**
     * Check if a route is defined from its key.
     *
     * @param string $routeKey
     *
     * @return bool
     */
    public function isRouteDefined(string $routeKey): bool
    {
        return (isset($this->getAttribute('routes')[$routeKey]) || ! empty($this->getAttribute('routes')[$routeKey]));
    }
}
