<?php

namespace Okipa\LaravelBootstrapTableList;

use Closure;
use ErrorException;
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
        // we set the default attribute values
        $this->attributes = [
            'sortableColumns'   => new Collection(),
            'searchableColumns' => new Collection(),
            'routes'            => new Collection(),
            'columns'           => new Collection(),
            'rowsNumber'        => config('tablelist.default.rows_number'),
        ];

        return parent::__construct();
    }

    /**
     * Set the model used for the table list generation (required)
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
     * Set the request used for the table list generation (required)
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
     * Set the routes used for the table list generation (required)
     *
     * @param array $routes
     *
     * @return $this
     */
    public function setRoutes(array $routes)
    {
        // we set the authorized array keys and values
        $requiredRouteKeys = ['index'];
        $authorizedRouteKeys = array_merge($requiredRouteKeys, ['create', 'edit', 'destroy']);
        $authorizedRouteParams = ['alias', 'parameters'];
        // we check the required routes are given
        $routeKeys = array_keys($routes);
        foreach ($requiredRouteKeys as $requiredRouteKey) {
            if (!in_array($requiredRouteKey, $routeKeys)) {
                throw new InvalidArgumentException(
                    'Invalid $routes argument for the setRoutes() method. Missing required "'
                    . $requiredRouteKey . '" array key.'
                );
            };
        }
        // we check if the given optional routes structure is correct
        foreach ($routes as $routeKey => $route) {
            if (!in_array($routeKey, $authorizedRouteKeys)) {
                throw new InvalidArgumentException(
                    'Invalid $routes argument for the setRoutes() method. The "' . $routeKey
                    . '" route key is not an authorized keys (' . implode(', ', $authorizedRouteKeys) . ').'
                );
            }
            foreach ($authorizedRouteParams as $authorizedRouteParam) {
                if (!in_array($authorizedRouteParam, array_keys($routes[$routeKey]))) {
                    throw new InvalidArgumentException(
                        'Invalid routes argument for $routes() method. The key "'
                        . $authorizedRouteParam . '" is missing from the "'
                        . $routeKey
                        . '" route definition. Each route must contain an array with a "alias" (string) key and a '
                        . '"parameters" (array) key. Check the following example : '
                        . '["index" => ["alias" => "news.index","parameters" => []].'
                    );
                }
            }
        }
        // we set the routes
        $this->routes = $routes;

        return $this;
    }

    /**
     * Set a custom number of rows for the table list (optional)
     *
     * @param int $rows_number
     *
     * @return $this
     */
    public function setRowsNumber(int $rows_number)
    {
        $this->rowsNumber = $rows_number;

        return $this;
    }

    /**
     * Enables the rows number selection in the table list (optional)
     *
     * @return $this|mixed
     */
    public function enableRowsNumberSelector()
    {
        $this->rowsNumberSelectorEnabled = true;

        return $this;
    }

    /**
     * Set the query closure that will be used during the table list generation (optional)
     * For example, you can define your joined tables here
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
     * Add a column that will be displayed in the table list (required)
     *
     * @param string|null $attribute
     *
     * @return TableListColumn
     * @throws ErrorException
     */
    public function addColumn(string $attribute = null)
    {
        // we check if the model has correctly been defined
        if (!$this->tableModel instanceof Model) {
            $errorMessage = 'The table list model has not been defined or is not an instance of ' . Model::class . '.';
            throw new ErrorException($errorMessage);
        }
        // we check if the request has correctly been defined
        if (!$this->request instanceof Request) {
            $errorMessage =
                'The table list request has not been defined or is not an instance of ' . Request::class . '.';
            throw new ErrorException($errorMessage);
        }
        $column = new TableListColumn($this, $attribute);
        $this->columns[] = $column;

        return $column;
    }

    /**
     * Get the searchable columns titles
     *
     * @return mixed
     */
    public function getSearchableTitles()
    {
        return $this->searchableColumns->implode('title', ', ');
    }

    /**
     * Get the columns count
     *
     * @return int
     */
    public function getColumnsCount()
    {
        return count($this->columns);
    }

    /**
     * Get the route from its key
     *
     * @param string $routeKey
     * @param array  $params
     *
     * @return string
     */
    public function getRoute(string $routeKey, array $params = [])
    {
        if (!isset($this->routes[$routeKey]) || empty($this->routes[$routeKey])) {
            throw new InvalidArgumentException(
                'Invalid $routeKey argument for the route() method. The route key «'
                . $routeKey . '» has not been found in the routes stack.'
            );
        }

        return route($this->routes[$routeKey]['alias'], array_merge($this->routes[$routeKey]['parameters'], $params));
    }

    /**
     * Check if a route is defined from its key
     *
     * @param string $routeKey
     *
     * @return bool
     */
    public function isRouteDefined(string $routeKey)
    {
        return (isset($this->routes[$routeKey]) || !empty($this->routes[$routeKey]));
    }

    /**
     * Get the navigation status from the table list
     *
     * @return \Illuminate\Contracts\Translation\Translator|string
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
     * Generate the table list html
     *
     * @return string
     */
    public function render()
    {
        // we check the columns validity
        $this->checkColumnsValidity();
        // we handle the request values
        $this->handleRequest();
        // we generate the list
        $this->generateEntitiesListFromQuery();

        return View::make('tablelist::table', ['table' => $this])->render();
    }

    /**
     * Check the given attributes validity in each table list column
     *
     * @throws ErrorException
     */
    private function checkColumnsValidity()
    {
        // check if at least one column has been declared
        if (!count($this->columns)) {
            // we prepare the error message
            $errorMessage = 'No column has been added to the table list. Please add at least one column by using the '
                            . '"addColumn" method on the table list object.';
            // we throw an exception
            throw new ErrorException($errorMessage);
        }
        $this->columns->map(function (TableListColumn $column) {
            // we check that the given column attribute is correct
            if (!is_null($column->attribute)
                && !in_array(
                    $column->attribute,
                    Schema::getColumnListing($column->customColumnTable)
                )) {
                // we prepare the error message
                $errorMessage = 'The given column attribute "' . $column->attribute . '" does not exist in the "'
                                . $column->customColumnTable . '" table.';
                // we throw an exception
                throw new ErrorException($errorMessage);
            }
            // we check if a title has been defined
            if (!$column->title) {
                // we prepare the error message
                $errorMessage = 'The given column "' . $column->attribute
                                . '" has no defined title. Please define a title by using the "setTitle()" '
                                . 'method on the column object.';
                // we throw an exception
                throw new ErrorException($errorMessage);
            }
        });
        if (!$this->destroyAttribute) {
            // we prepare the error message
            $errorMessage =
                'No column attribute has been choosed for the destroy confirmation. '
                . 'Please define an attribute by using the "useForDestroyConfirmation()" method on a column object.';
            // we throw an exception
            throw new ErrorException($errorMessage);
        }
    }

    /**
     * Handle the request treatments
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
     * Generate the entities list
     *
     * @throws ErrorException
     */
    private function generateEntitiesListFromQuery()
    {
        // we instantiate the query
        $query = $this->tableModel->query();
        // closure treatment
        if ($closure = $this->queryClosure) {
            // we execute the given closure
            $closure($query);
        }
        // search treatment
        if ($searched = $this->request->search) {
            $this->searchableColumns->map(function (TableListColumn $column, int $key) use (&$query, $searched) {
                // we set the attribute to query
                $attribute = $column->customColumnTable . '.' . $column->attribute;
                // we add the search query
                if ($key > 0) {
                    $query->orWhere($attribute, 'like', '%' . $searched . '%');
                } else {
                    $query->where($attribute, 'like', '%' . $searched . '%');
                }
            });
        }
        // sort treatment
        if (($sortBy = $this->request->get('sortBy', $this->sortBy))
            && ($sortDir = $this->request->get('sortDir', $this->sortDir))) {
            $query->orderBy($sortBy, $sortDir);
        } else {
            $errorMessage = 'No default column has been selected for the table sort. '
                            . 'Please define a column sorted by default by using the "sortByDefault()" method.';
            throw new ErrorException($errorMessage);
        }
        // pagination treatment
        $this->list = $query->paginate($this->rowsNumber);
        $this->list->appends([
            'rowsNumber' => $this->rowsNumber,
            'search'     => $this->search,
            'sortBy'     => $this->sortBy,
            'sortDir'    => $this->sortDir,
        ]);
    }
}
