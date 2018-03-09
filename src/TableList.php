<?php

namespace Okipa\LaravelBootstrapTableList;

use Closure;
use ErrorException;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use InvalidArgumentException;
use Log;
use Schema;
use Validator;
use View;

class TableList extends Model
{
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
        'list',
        'destroyAttribute',
    ];

    /**
     * TableList constructor.
     */
    public function __construct()
    {
        parent::__construct([
            'rowsNumber'        => config('tablelist.default.rows_number'),
            'sortableColumns'   => new Collection(),
            'searchableColumns' => new Collection(),
            'request'           => request(),
            'routes'            => [],
            'columns'           => new Collection(),
        ]);
    }

    /**
     * Set the model used for the table list generation (required).
     *
     * @param string $tableModel
     *
     * @return $this
     */
    public function setModel(string $tableModel)
    {
        $this->tableModel = app()->make($tableModel);

        return $this;
    }

    /**
     * Set the request used for the table list generation (required).
     *
     * @param Request $request
     *
     * @return $this
     */
    public function setRequest(Request $request)
    {
        $this->request = $request;

        return $this;
    }

    /**
     * Set the routes used for the table list generation (required).
     *
     * @param array $routes
     *
     * @return $this
     * @throws \ErrorException
     */
    public function setRoutes(array $routes)
    {
        $this->checkRoutesValidity($routes);
        $this->routes = $routes;

        return $this;
    }

    /**
     * Check routes validity
     *
     * @param array $routes
     *
     * @throws \ErrorException
     */
    private function checkRoutesValidity(array $routes)
    {
        $requiredRouteKeys = ['index'];
        $optionalRouteKeys = ['create', 'edit', 'destroy'];
        $allowedRouteKeys = array_merge($requiredRouteKeys, $optionalRouteKeys);
        $this->checkRequiredRoutesValidity($routes, $requiredRouteKeys);
        $this->checkAllowedRoutesValidity($routes, $allowedRouteKeys);
        $this->checkRoutesStructureValidity($routes);
    }

    /**
     * Check required routes validity
     *
     * @param array $routes
     * @param array $requiredRouteKeys
     *
     * @throws \ErrorException
     */
    private function checkRequiredRoutesValidity(array $routes, array $requiredRouteKeys)
    {
        $routeKeys = array_keys($routes);
        foreach ($requiredRouteKeys as $requiredRouteKey) {
            if (! in_array($requiredRouteKey, $routeKeys)) {
                throw new ErrorException(
                    'The required "' . $requiredRouteKey
                    . '" route key is missing. Please use the setRoutes() method to declare it.'
                );
            }
        }
    }

    /**
     * Check allowed routes validity
     *
     * @param array $routes
     * @param array $allowedRouteKeys
     *
     * @throws \ErrorException
     */
    private function checkAllowedRoutesValidity(array $routes, array $allowedRouteKeys)
    {
        foreach ($routes as $routeKey => $route) {
            if (! in_array($routeKey, $allowedRouteKeys)) {
                throw new ErrorException(
                    'The "' . $routeKey . '" key is not an authorized route key (' . implode(', ', $allowedRouteKeys)
                    . '). Please correct your routes declaration using the setRoutes() method.'
                );
            }
        }
    }

    /**
     * Check routes structure validity
     *
     * @param array $routes
     *
     * @throws \ErrorException
     */
    private function checkRoutesStructureValidity(array $routes)
    {
        $requiredRouteParams = ['alias', 'parameters'];
        foreach ($routes as $routeKey => $route) {
            foreach ($requiredRouteParams as $requiredRouteParam) {
                if (! in_array($requiredRouteParam, array_keys($route))) {
                    throw new ErrorException(
                        'The "' . $requiredRouteParam . '" key is missing from the "' . $routeKey
                        . '" route definition. Each route key must contain an array with a (string) "alias" key and a '
                        . '(array) "parameters" value. Check the following example : '
                        . '["index" => ["alias" => "news.index","parameters" => []]. '
                        . 'Please correct your routes declaration using the setRoutes() method.'
                    );
                }
            }
        }
    }

    /**
     * Set a custom number of rows for the table list (optional).
     *
     * @param int $rowsNumber
     *
     * @return $this
     */
    public function setRowsNumber(int $rowsNumber)
    {
        $this->rowsNumber = $rowsNumber;

        return $this;
    }

    /**
     * Enables the rows number selection in the table list (optional).
     *
     * @return TableList
     */
    public function enableRowsNumberSelector()
    {
        $this->rowsNumberSelectorEnabled = true;

        return $this;
    }

    /**
     * Set the query closure that will be used during the table list generation (optional)
     * For example, you can define your joined tables here.
     *
     * @param Closure $queryClosure
     *
     * @return $this
     */
    public function addQueryInstructions(Closure $queryClosure)
    {
        $this->queryClosure = $queryClosure;

        return $this;
    }

    /**
     * Add a column that will be displayed in the table list (required).
     *
     * @param string|null $attribute
     *
     * @return TableListColumn
     * @throws ErrorException
     */
    public function addColumn(string $attribute = null)
    {
        // we check if the model has correctly been defined
        if (! $this->tableModel instanceof Model) {
            $errorMessage = 'The table list model has not been defined or is not an instance of ' . Model::class . '.';
            throw new ErrorException($errorMessage);
        }
        $column = new TableListColumn($this, $attribute);
        $this->columns[] = $column;

        return $column;
    }

    /**
     * Get the searchable columns titles.
     *
     * @return string
     */
    public function getSearchableTitles()
    {
        return $this->searchableColumns->implode('title', ', ');
    }

    /**
     * Get the columns count.
     *
     * @return int
     */
    public function getColumnsCount()
    {
        return count($this->columns);
    }

    /**
     * Get the route from its key.
     *
     * @param string $routeKey
     * @param array  $params
     *
     * @return string
     */
    public function getRoute(string $routeKey, array $params = [])
    {
        if (! isset($this->routes[$routeKey]) || empty($this->routes[$routeKey])) {
            throw new InvalidArgumentException(
                'Invalid $routeKey argument for the route() method. The route key «'
                . $routeKey . '» has not been found in the routes stack.'
            );
        }

        return route($this->routes[$routeKey]['alias'], array_merge($this->routes[$routeKey]['parameters'], $params));
    }

    /**
     * Get the navigation status from the table list.
     *
     * @return string
     */
    public function navigationStatus()
    {
        return trans('tablelist::tablelist.tfoot.nav', [
            'start' => ($this->list->perPage() * ($this->list->currentPage() - 1)) + 1,
            'stop'  => $this->list->count() + (($this->list->currentPage() - 1) * $this->list->perPage()),
            'total' => $this->list->total(),
        ]);
    }

    /**
     * Generate the table list html.
     *
     * @return string
     * @throws ErrorException
     */
    public function render()
    {
        $this->checkRoutesValidity($this->routes);
        $this->checkColumnsValidity();
        $this->checkDestroyAttributeDefinition();
        $this->handleRequest();
        $this->generateEntitiesListFromQuery();

        return View::make('tablelist::table', ['table' => $this])->render();
    }

    /**
     * Check the given attributes validity in each table list column.
     *
     * @throws ErrorException
     */
    private function checkColumnsValidity()
    {
        $this->checkIfAtLeastOneColumnIsDeclared();
        $this->columns->map(function(TableListColumn $column) {
            $this->checkColumnAttributeExistence($column);
            $this->checkColumnTitleDefinition($column);
        });
    }

    /**
     * Check if at least one column is declared.
     *
     * @throws \ErrorException
     */
    private function checkIfAtLeastOneColumnIsDeclared()
    {
        if (! count($this->columns)) {
            // we prepare the error message
            $errorMessage = 'No column has been added to the table list. Please add at least one column by using the '
                            . '"addColumn" method on the table list object.';
            // we throw an exception
            throw new ErrorException($errorMessage);
        }
    }

    /**
     * Check that the column attribute exists.
     *
     * @param \Okipa\LaravelBootstrapTableList\TableListColumn $column
     *
     * @throws \ErrorException
     */
    private function checkColumnAttributeExistence(TableListColumn $column)
    {
        if (
            ! is_null($column->attribute)
            && ! in_array(
                $column->attribute,
                Schema::getColumnListing($column->customColumnTable)
            )
        ) {
            // we prepare the error message
            $errorMessage = 'The given column attribute "' . $column->attribute . '" does not exist in the "'
                            . $column->customColumnTable . '" table.';
            // we throw an exception
            throw new ErrorException($errorMessage);
        }
    }

    /**
     * Check that the column title has been defined.
     *
     * @param \Okipa\LaravelBootstrapTableList\TableListColumn $column
     *
     * @throws \ErrorException
     */
    private function checkColumnTitleDefinition(TableListColumn $column)
    {
        if (! $column->title) {
            // we prepare the error message
            $errorMessage = 'The given column "' . $column->attribute
                            . '" has no defined title. Please define a title by using the "setTitle()" '
                            . 'method on the column object.';
            // we throw an exception
            throw new ErrorException($errorMessage);
        }
    }

    /**
     * Check that a destroy attribute has been defined.
     *
     * @throws \ErrorException
     */
    private function checkDestroyAttributeDefinition()
    {
        if ($this->isRouteDefined('destroy') && ! $this->destroyAttribute) {
            // we prepare the error message
            $errorMessage =
                'No column attribute has been choosed for the destroy confirmation. '
                . 'Please define an attribute by using the "useForDestroyConfirmation()" method on a column object.';
            // we throw an exception
            throw new ErrorException($errorMessage);
        }
    }

    /**
     * Check if a route is defined from its key.
     *
     * @param string $routeKey
     *
     * @return bool
     */
    public function isRouteDefined(string $routeKey)
    {
        return (isset($this->routes[$routeKey]) || ! empty($this->routes[$routeKey]));
    }

    /**
     * Handle the request treatments.
     */
    private function handleRequest()
    {
        // we check the inputs validity
        $validator = Validator::make($this->request->only('rowsNumber', 'search', 'sortBy', 'sortDir'), [
            'rowsNumber' => 'required|numeric',
            'search'     => 'nullable|string',
            'sortBy'     => 'nullable|string|in:' . $this->columns->implode('attribute', ','),
            'sortDir'    => 'nullable|string|in:asc,desc',
        ]);
        // if errors are found
        if ($validator->fails()) {
            // we log the errors
            Log::error($validator->errors());
            // we set back the default values
            $this->request->merge([
                'rowsNumber' => $this->rowsNumber ? $this->rowsNumber : config('tablelist.default.rows_number'),
                'search'     => null,
                'sortBy'     => $this->sortBy,
                'sortDir'    => $this->sortDir,
            ]);
        } else {
            // we save the request values
            $this->rowsNumber = $this->request->rowsNumber;
            $this->search = $this->request->search;
        }
    }

    /**
     * Generate the entities list.
     *
     * @throws ErrorException
     */
    private function generateEntitiesListFromQuery()
    {
        $query = $this->tableModel->query();
        $this->applyQueryClosure($query);
        $this->applySearchClauses($query);
        $this->applySortClauses($query);
        $this->paginateList($query);
    }

    /**
     * Apply query closure
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     */
    private function applyQueryClosure(Builder $query)
    {
        if ($closure = $this->queryClosure) {
            $closure($query);
        }
    }

    /**
     * Apply search clauses
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     */
    private function applySearchClauses(Builder $query)
    {
        if ($searched = $this->request->search) {
            $this->searchableColumns->map(function(TableListColumn $column, int $key) use (&$query, $searched) {
                $attribute = $column->customColumnTable . '.' . $column->attribute;
                if ($key > 0) {
                    $query->orWhere($attribute, 'like', '%' . $searched . '%');
                } else {
                    $query->where($attribute, 'like', '%' . $searched . '%');
                }
            });
        }
    }

    /**
     * Apply sort clauses
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     *
     * @throws \ErrorException
     */
    private function applySortClauses(Builder $query)
    {
        if (
            ($sortBy = $this->request->get('sortBy', $this->sortBy))
            && ($sortDir = $this->request->get('sortDir', $this->sortDir))
        ) {
            $query->orderBy($sortBy, $sortDir);
        } else {
            $errorMessage = 'No default column has been selected for the table sort. '
                            . 'Please define a column sorted by default by using the "sortByDefault()" method.';
            throw new ErrorException($errorMessage);
        }
    }

    /**
     * Paginate the list from the query
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     */
    private function paginateList(Builder $query)
    {
        $this->list = $query->paginate($this->rowsNumber);
        $this->list->appends([
            'rowsNumber' => $this->rowsNumber,
            'search'     => $this->search,
            'sortBy'     => $this->sortBy,
            'sortDir'    => $this->sortDir,
        ]);
    }
}
