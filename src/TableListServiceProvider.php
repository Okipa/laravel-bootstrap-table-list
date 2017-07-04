<?php

namespace Okipa\LaravelBootstrapTableList;

use Illuminate\Foundation\AliasLoader;
use Illuminate\Support\ServiceProvider;
use Okipa\LaravelToggleSwitchButton\Facades\ToggleSwitchButton;
use Okipa\LaravelToggleSwitchButton\ToggleSwitchButtonServiceProvider;

class TableListServiceProvider extends ServiceProvider
{
    
    public function boot()
    {
        $this->loadViewsFrom(__DIR__ . '/../views', 'tablelist');
        
        $this->loadTranslationsFrom(__DIR__.'/../lang', 'tablelist');
        
        $this->publishes([
            __DIR__ . '/../config/tablelist.php' => config_path('tablelist.php'),
        ], 'tablelist::config');
        
        $this->publishes([
            __DIR__ . '/../lang'  => resource_path('lang'),
        ], 'tablelist::translations');
        
        $this->publishes([
            __DIR__ . '/../views' => resource_path('views/vendor/tablelist'),
        ], 'tablelist::views');
    }
    
    /**
     * {@inheritDoc}
     */
    public function register()
    {
        $this->mergeConfigFrom(
            __DIR__.'/../config/tablelist.php', 'tablelist'
        );
        
        $this->app->singleton('Okipa\TableList', function ($app) {
            $tableList = $app->make(TableList::class);
            
            return $tableList;
        });
    }
}
