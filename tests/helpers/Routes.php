<?php

namespace Okipa\LaravelBootstrapTableList\Test\helpers;

trait Routes
{
    public function setRoutes(array $entities = ['users'], array $routes= ['index'])
    {
        foreach ($entities as $entity) {
            foreach ($routes as $route){
                app('router')->get('/' . $entity . '/' . $route, [
                    'as' => $entity . '.' . $route, function() use ($entity, $route) {
                        return $entity . '.' . $route . ' route.';
                    },
                ]);
            }
        }
    }
}
