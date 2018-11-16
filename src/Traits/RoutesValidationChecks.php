<?php

namespace Okipa\LaravelBootstrapTableList\Traits;

use ErrorException;
use InvalidArgumentException;

trait RoutesValidationChecks
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
     * Check routes validity.
     *
     * @param array $routes
     *
     * @return void
     * @throws \ErrorException
     */
    protected function checkRoutesValidity(array $routes): void
    {
        $requiredRouteKeys = ['index'];
        $optionalRouteKeys = ['create', 'edit', 'destroy'];
        $allowedRouteKeys = array_merge($requiredRouteKeys, $optionalRouteKeys);
        $this->checkRequiredRoutesValidity($routes, $requiredRouteKeys);
        $this->checkAllowedRoutesValidity($routes, $allowedRouteKeys);
        $this->checkRoutesStructureValidity($routes);
    }

    /**
     * Check required routes validity.
     *
     * @param array $routes
     * @param array $requiredRouteKeys
     *
     * @return void
     * @throws \ErrorException
     */
    protected function checkRequiredRoutesValidity(array $routes, array $requiredRouteKeys): void
    {
        $routeKeys = array_keys($routes);
        foreach ($requiredRouteKeys as $requiredRouteKey) {
            if (! in_array($requiredRouteKey, $routeKeys)) {
                throw new ErrorException(
                    'The required « ' . $requiredRouteKey
                    . ' » route key is missing. Use the « setRoutes() » method to declare it.'
                );
            }
        }
    }

    /**
     * Check allowed routes validity.
     *
     * @param array $routes
     * @param array $allowedRouteKeys
     *
     * @return void
     * @throws \ErrorException
     */
    protected function checkAllowedRoutesValidity(array $routes, array $allowedRouteKeys): void
    {
        foreach ($routes as $routeKey => $route) {
            if (! in_array($routeKey, $allowedRouteKeys)) {
                throw new ErrorException(
                    'The « ' . $routeKey . ' » key is not an authorized route key (' . implode(', ', $allowedRouteKeys)
                    . '). Fix your routes declaration in the « setRoutes() » method.'
                );
            }
        }
    }

    /**
     * Check routes structure validity.
     *
     * @param array $routes
     *
     * @return void
     * @throws \ErrorException
     */
    protected function checkRoutesStructureValidity(array $routes): void
    {
        $requiredRouteParams = ['alias', 'parameters'];
        foreach ($routes as $routeKey => $route) {
            foreach ($requiredRouteParams as $requiredRouteParam) {
                if (! in_array($requiredRouteParam, array_keys($route))) {
                    throw new ErrorException(
                        'The « ' . $requiredRouteParam . ' » key is missing from the « ' . $routeKey
                        . ' » route definition. Each route key must contain an array with a (string) '
                        . '« alias » key and a (array) « parameters » value. Check the following example : '
                        . '["index" => ["alias" => "news.index","parameters" => []]. '
                        . 'Fix your routes declaration in the « setRoutes() » method.'
                    );
                }
            }
        }
    }

    protected function checkRouteIsDefined(string $routeKey)
    {
        if (! isset($this->getAttribute('routes')[$routeKey]) || empty($this->getAttribute('routes')[$routeKey])) {
            throw new InvalidArgumentException(
                'Invalid « $routeKey » argument for the « route() » method. The route key « '
                . $routeKey . ' » has not been found in the routes stack.'
            );
        }
    }
}
