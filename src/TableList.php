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
        'model',
        'rows_number',
        'rows_number_selector_enabled',
        'sortable_columns',
        'sortBy',
        'sortDir',
        'searchable_columns',
        'request',
        'routes',
        'columns',
        'query_closure',
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
            'sortable_columns'   => new Collection(),
            'searchable_columns' => new Collection(),
            'routes'             => new Collection(),
            'columns'            => new Collection(),
            'rows_number'        => config('tablelist.default.rows_number'),
        ];
        
        return parent::__construct();
    }
    
    /**
     * Set the model used for the table list generation (required)
     *
     * @param string $model
     *
     * @return $this
     */
    function setModel(string $model)
    {
        $this->model = app()->make($model);
        
        return $this;
    }
    
    /**
     * Set the request used for the table list generation (required)
     *
     * @param Request $request
     *
     * @return $this
     */
    function setRequest(Request $request)
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
    function setRoutes(array $routes)
    {
        // we set the authorized array keys and values
        $requested_routes_keys = ['index'];
        $authorized_routes_keys = array_merge($requested_routes_keys, ['create', 'edit', 'destroy', 'activation']);
        $authorized_route_params = ['alias', 'parameters'];
        
        // we check the requested routes are given
        $routes_keys = array_keys($routes);
        foreach ($requested_routes_keys as $requested_route_key) {
            if (!in_array($requested_route_key, $routes_keys)) {
                throw new InvalidArgumentException('Invalid argument for routes method. Missing requested "' . $requested_route_key . '" array key.');
            };
        }
        
        // we check if the given optional routes structure is correct
        foreach ($routes as $route_key => $route) {
            if (!in_array($route_key, $authorized_routes_keys)) {
                throw new InvalidArgumentException('Invalid argument for routes method. The "' . $route_key . '" route key must be one the following keys : ' . implode(', ', $authorized_routes_keys));
            }
            foreach ($authorized_route_params as $authorized_route_param) {
                if (!in_array($authorized_route_param, array_keys($routes[$route_key]))) {
                    throw new InvalidArgumentException('Invalid routes argument for $routes() method. The key "' . $authorized_route_param . '" is missing from the "' . $route_key . '" route definition. Each route must contain an array with a "alias" (string) key and a "parameters" (array) key. Check the following example : ["index" => ["alias" => "news.index","parameters" => []]');
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
    function setRowsNumber(int $rows_number)
    {
        $this->rows_number = $rows_number;
        
        return $this;
    }
    
    /**
     * Enables the rows number selection in the table list (optional)
     *
     * @return $this|mixed
     *
     */
    function enableRowsNumberSelector()
    {
        $this->rows_number_selector_enabled = true;
        
        return $this;
    }
    
    /**
     * Set the query closure that will be used during the table list generation (optional)
     * For example, you can define your joined tables here
     *
     * @param Closure $query_closure
     *
     * @return $this
     */
    public function addQueryInstructions(Closure $query_closure)
    {
        $this->query_closure = $query_closure;
        
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
        if (!$this->model instanceof Model) {
            $errorMessage = 'The table list model has not been defined or is not an instance of ' . Model::class . '.';
            throw new ErrorException($errorMessage);
        }
        // we check if the request has correctly been defined
        if (!$this->request instanceof Request) {
            $errorMessage = 'The table list request has not been defined or is not an instance of ' . Request::class . '.';
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
        return $this->searchable_columns->implode('title', ', ');
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
     * @param array $params
     *
     * @return string
     */
    public function getRoute(string $routeKey, array $params = [])
    {
        if (!isset($this->routes[$routeKey]) || empty($this->routes[$routeKey])) {
            throw new InvalidArgumentException('Invalid $routeKey argument for the route() method. The route key «' . $routeKey . '» has not been found in the routes stack.');
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
     * Check the given attributes validity in each table list column
     *
     * @throws ErrorException
     */
    protected function checkColumnsValidity()
    {
        // check if at least one column has been declared
        if (!count($this->columns)) {
            // we prepare the error message
            $errorMessage = 'No column has been added to the table list. Please add at least one column by using the "addColumn" method on the table list object.';
            // we throw an exception
            throw new ErrorException($errorMessage);
        }
        $this->columns->map(function ($column) {
            // we check that the given column attribute is correct
            if (!is_null($column->attribute) && !in_array($column->attribute, Schema::getColumnListing($column->column_table))) {
                // we prepare the error message
                $errorMessage = 'The given column attribute "' . $column->attribute . '" does not exist in the "' . $column->column_table . '" table.';
                // we throw an exception
                throw new ErrorException($errorMessage);
            }
            // we check if a title has been defined
            if (!$column->title) {
                // we prepare the error message
                $errorMessage = 'The given column "' . $column->attribute . '" has no defined title. Please define a title by using the "setTitle()" method on the column object.';
                // we throw an exception
                throw new ErrorException($errorMessage);
            }
            // if the column is an activation toggle, we check that the activation has been defined
            if ($column->is_activation_toggle) {
                if (!$this->isRouteDefined('activation')) {
                    // we prepare the error message
                    $errorMessage = 'The given column "' . $column->attribute . '" has been defined as an activation toggle. No "activation" route has been defined. Please define one in the "setRoutes()" method on the table list object.';
                    // we throw an exception
                    throw new ErrorException($errorMessage);
                }
            }
        });
        if (!$this->destroyAttribute) {
            // we prepare the error message
            $errorMessage = 'No column attribute has been choosed for the destroy confirmation. Please define an attribute by using the "useForDestroyConfirmation()" method on a column object.';
            // we throw an exception
            throw new ErrorException($errorMessage);
        }
    }
    
    /**
     * Handle the request treatments
     */
    protected function handleRequest()
    {
        // we check the inputs validity
        $validator = Validator::make($this->request->only('rows_number', 'search', 'sortBy', 'sortDir'), [
            'rows_number' => 'required|numeric',
            'search'      => 'nullable|string',
            'sortBy'      => 'nullable|string|in:' . $this->columns->implode('attribute', ','),
            'sortDir'     => 'nullable|string|in:asc,desc',
        ]);
        // if errors are found
        if ($validator->fails()) {
            // we log the errors
            Log::error($validator->errors());
            // we set back the default values
            $this->request->merge([
                'rows_number' => $this->rows_number ? $this->rows_number : config('tablelist.default.rows_number'),
                'search'      => null,
                'sortBy'      => $this->sortBy,
                'sortDir'     => $this->sortDir,
            ]);
        } else {
            // we save the request values
            $this->rows_number = $this->request->rows_number;
            $this->search = $this->request->search;
        }
    }
    
    /**
     * Generate the entities list
     *
     * @throws ErrorException
     */
    protected function generateEntitiesListFromQuery()
    {
        // we instantiate the query
        $query = $this->model->query();
        // closure treatment
        if ($closure = $this->query_closure) {
            // we execute the given closure
            $closure($query);
        }
        // search treatment
        if ($searched = $this->request->search) {
            $this->searchable_columns->map(function ($column, $key) use (&$query, $searched) {
                // we set the attribute to query
                $attribute = $column->column_table . '.' . $column->attribute;
                // we add the search query
                if ($key > 0) {
                    $query->orWhere($attribute, 'like', '%' . $searched . '%');
                } else {
                    $query->where($attribute, 'like', '%' . $searched . '%');
                }
            });
        }
        // sort treatment
        if (($sortBy = $this->request->get('sortBy', $this->sortBy)) && ($sortDir = $this->request->get('sortDir', $this->sortDir))) {
            $query->orderBy($this->request->sortBy, $this->request->sortDir);
        } else {
            $errorMessage = 'No default column has been selected for the table sort. Please define a column sorted by default by using the "sortByDefault()" method.';
            throw new ErrorException($errorMessage);
        }
        // pagination treatment
        $this->list = $query->paginate($this->rows_number);
        $this->list->appends([
            'rows_number' => $this->rows_number,
            'search'      => $this->search,
            'sortBy'      => $this->sortBy,
            'sortDir'     => $this->sortDir,
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
}
