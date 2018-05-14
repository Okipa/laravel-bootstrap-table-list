# Laravel Bootstrap Table List

[![Source Code](https://img.shields.io/badge/source-okipa/laravel--bootstrap--table--list-blue.svg)](https://github.com/Okipa/laravel-bootstrap-table-list)
[![Latest Version](https://img.shields.io/github/release/okipa/laravel-bootstrap-table-list.svg?style=flat-square)](https://github.com/Okipa/laravel-bootstrap-table-list/releases)
[![Total Downloads](https://img.shields.io/packagist/dt/okipa/laravel-bootstrap-table-list.svg?style=flat-square)](https://packagist.org/packages/okipa/laravel-bootstrap-table-list)
[![License: MIT](https://img.shields.io/badge/License-MIT-blue.svg)](https://opensource.org/licenses/MIT)
[![Build Status](https://scrutinizer-ci.com/g/Okipa/laravel-bootstrap-table-list/badges/build.png?b=master)](https://scrutinizer-ci.com/g/Okipa/laravel-bootstrap-table-list/build-status/master)
[![Code Coverage](https://scrutinizer-ci.com/g/Okipa/laravel-bootstrap-table-list/badges/coverage.png?b=master)](https://scrutinizer-ci.com/g/Okipa/laravel-bootstrap-table-list/?branch=master)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/Okipa/laravel-bootstrap-table-list/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/Okipa/laravel-bootstrap-table-list/?branch=master)

Because it is sometimes convenient to build a simple backoffice without sophisticated javascript treatments, Laravel Bootstrap Table List proposes a model-based and highly customizable php table list generation, that simply render your table HTML in your view, with a controller-side-configuration.

![Laravel Bootstrap Table List](https://raw.githubusercontent.com/Okipa/laravel-bootstrap-table-list/master/img/laravel-bootstrap-table-list.png)

------------------------------------------------------------------------------------------------------------------------

## Installation

- Install the package with composer :
```bash
composer require okipa/laravel-bootstrap-table-list
```

- Laravel 5.5+ uses Package Auto-Discovery, so doesn't require you to manually add the ServiceProvider.
If you don't use auto-discovery or if you use a Laravel 5.4- version, add the package service provider in the `register()` method from your `app/Providers/AppServiceProvider.php` :
```php
// laravel bootstrap table list
// https://github.com/Okipa/laravel-bootstrap-table-list
$this->app->register(Okipa\LaravelBootstrapTableList\TableListServiceProvider::class);
```

- Load the package `CSS` or `SASS` file from the `[path/to/composer/vendor]/okipa/laravel-bootstrap-table-list/styles` directory to your project.

------------------------------------------------------------------------------------------------------------------------

## Package usage

### Basic usage

In your controller, simply call the package like the following example to generate your table list :
```php
// we instantiate a table list in the news controller
$table = app(TableList::class)
    ->setModel(News::class)
    ->setRoutes([
        'index' => ['alias' => 'news.index', 'parameters' => []],
    ]);
// we add some columns to the table list
$table->addColumn('title')
    ->setTitle('Title')
    ->isSortable()
    ->isSearchable()
    ->useForDestroyConfirmation();
$table->addColumn('content')
    ->setTitle('Content')
    ->setStringLimit(30);
$table->addColumn('created_at')
    ->setTitle('Creation date')
    ->isSortable()
    ->setColumnDateFormat('d/m/Y H:i:s');
$table->addColumn('updated_at')
    ->setTitle('Update date')
    ->isSortable()
    ->setColumnDateFormat('d/m/Y H:i:s');
```
Then, send your `$table` object in your view and render your table list :
```php
{!! $table->render() !!}
```
That's it !

### Notes :

- **Request** : No need to transmit the request to the TableList : it systematically uses the current request given by the `request()` helper to get the number of lines to show and the searching, sorting or pagination data. However, if you need to pass a particular request to the TableList, you can do it with the `setRequest()` method.

### Advanced usage

If you need your table list for a more advanced usage, with a multilingual project for example, here is an example of what you can do in your controller :
```php
// we instantiate a table list in the news controller
$table = app(TableList::class)
    ->setModel(News::class)
    ->setRequest($request)
    ->setRoutes([
        'index'      => ['alias' => 'news.index', 'parameters' => []],
        'create'     => ['alias' => 'news.create', 'parameters' => []],
        'edit'       => ['alias' => 'news.edit', 'parameters' => []],
        'destroy'    => ['alias' => 'news.destroy', 'parameters' => []],
    ])
    ->setRowsNumber(20)
    ->enableRowsNumberSelector()
    ->addQueryInstructions(function ($query) {
        $query->select('news.*')
            ->join('news_translations', 'news.id', '=', 'news_translations.news_id')
            ->where('news_translations.locale', config('app.locale'));
    });
// we add columns
$table->addColumn('image')
    ->setTitle(trans('news.label.image'))
    ->isCustomHtmlElement(function ($entity, $column) {
        if ($src = $entity->{$column->attribute}) {
            $image_zoom_src = $entity->imagePath($src, $column->attribute, 'zoom');
            $image_thumbnail_src = $entity->imagePath($src, $column->attribute, 'thumbnail');

            return "<a href='$image_zoom_src' title='Image title' target="blank"><img class='thumbnail' src='$image_thumbnail_src' alt='Image alt'></a>";
        }
    });
$table->addColumn('title')
    ->setTitle(trans('news.label.title'))
    ->setCustomTable('news_translations')
    ->isSortable()
    ->isSearchable()
    ->useForDestroyConfirmation();
$table->addColumn('content')
    ->setTitle(trans('news.label.content'))
    ->setCustomTable('news_translations')
    ->setStringLimit(30);
$table->addColumn('category_id')
    ->setTitle(trans('news.label.category'))
    ->isButton('btn btn-default')
    ->isCustomValue(function ($entity, $column) {
        return config('news.category.' . $entity->{$column->attribute});
    });
$table->addColumn()->setTitle(trans('news.label.preview'))
    ->isCustomHtmlElement(function ($entity, $column) {
        $preview_route = route('news.preview', ['id' => $entity->id]);
        $preview_label = trans('global.action.preview');
        return "<a class='btn btn-primary' href='$preview_route'>$preview_label</a>";
    });
$table->addColumn('released_at')
    ->setTitle(trans('news.label.released_at'))
    ->isSortable()
    ->sortByDefault('desc')
    ->setColumnDateFormat('d/m/Y H:i:s');
$table->addColumn('created_at')
    ->setTitle(trans('news.label.created_at'))
    ->isSortable()
    ->setColumnDateFormat('d/m/Y H:i:s');
$table->addColumn('updated_at')
    ->setTitle(trans('news.label.updated_at'))
    ->isSortable()
    ->setColumnDateFormat('d/m/Y H:i:s');
```

------------------------------------------------------------------------------------------------------------------------

## API

### TableList public methods

- `setModel(string $tableModel)`
    > Set the model used for the table list generation (required).
- `setRequest(Request $request)`
    > Set the request used for the table list generation (required).
- `setRoutes(array $routes)`
    > Set the routes used for the table list generation (required).  
    
    > **Notes :**
    > - Each route will be generated with the line entity id. The given extra parameters will be added for the route generation.
    > - The `index` route is required and must be the route that will be used to display the page that contains the table list.
    > - The following routes can be defined as well :
    >     - `create` : must be used to redirect toward the entity creation page. Displays a `Create` button under the table list if defined.
    >     - `edit` : must be used to redirect toward the entity edition page. Displays a `Edit` icon on each table list line if defined.
    >     - `destroy` : must be used to destroy a table list line. Displays a `Remove` icon on each table list line if defined.
    > - Each route have to be defined with the following structure :
```php
'index' => [
    'alias' => 'news.index',
    'parameters' => [
        // set your extra parameters here or let the array empty
    ]
]
```
- `setRowsNumber(int $owsNumber)`
    > Set a custom number of rows for the table list (optional).
- `enableRowsNumberSelector()`
    > Enables the rows number selection in the table list (optional).  
    
    > **Notes :**
    > - Calling this method displays a rows number input that enable the user to choose how much rows to show.
- `addQueryInstructions(Closure $queryClosure)`
    > Set the query closure that will be used during the table list generation (optional).  
    
    > **Notes :**
    > - Use the `$query` parameter provided in the closure to add your custom instructions.
    > - For example, you use this closure to define your joined tables here.  
- `addColumn(string $attribute = null)`
    > Add a column that will be displayed in the table list (required).  
    
    > **Notes :**  
    > - at least one column must be added to the table list.  
    > - a column can be created without attribute specification, in case of HTML element display, for example.

### TableListColumn public methods

- `setTitle(string $title)`
    > Set the column title (required).
- `sortByDefault(string $direction = 'asc')`
    > Set the default sorted column (required).
- `useForDestroyConfirmation()``
    > Use the column attribute for the destroy confirmation message generation (required).
    > This method can be called only once.
- `isSortable()`
    > Make the column sortable (optional).
- `isSearchable()`
    > Make the column searchable (optional).
- `setCustomTable(string $customColumnTable)`
    > Set a custom table for the column (optional).  
    > Calling this method can be useful if the column attribute does not directly belong to the table list model.
- `setColumnDateFormat(string $columnDateFormat)`
    > Set the format for a date (optional).  
    > (Carbon is used to format the date).
- `isButton(string $buttonClass)`
    > Set the column button class (optional).
    > The attribute is wrapped into a button.
- `setStringLimit(int $stringLimit)`
    > Set the string value display limitation (optional).
    > Shows "..." when the limit is reached.
- `isLink(Closure $linkClosure)`
    > Set the link in the method closure (optional).
    > The closure let you manipulate the following attributes : $entity, $column.
- `isCustomValue(Closure $customValueClosure)`
    > Set a custom value in the method closure (optional).
    > The closure let you manipulate the following attributes : $entity, $column.
- `isCustomHtmlElement(Closure $customHtmlEltClosure)`
    > Set the HTML element to render in the method closure (optional).
    > The closure let you manipulate the following attributes : $entity, $column.

------------------------------------------------------------------------------------------------------------------------

## Configuration

To personalize the package configuration, you have to publish it first with the following script :
```bash
php artisan vendor:publish --tag=tablelist::config
```
Then, open the published package configuration file (`config/tablelist.php`) and override the default table list configuration by setting your own values for the following items :
- default number of displayed rows
- template configurations (buttons classes, buttons icons, ...)

------------------------------------------------------------------------------------------------------------------------

## Customize styles

Simply override the `CSS` or `SASS` to customize the package styles.

------------------------------------------------------------------------------------------------------------------------

## Customize templates

Publish the package blade templates file in your project :
```bash
php artisan vendor:publish --tag=tablelist::views
```
Then, change the content from the package templates in your `resources/views/vendor/tablelist` directory.

------------------------------------------------------------------------------------------------------------------------

## Contributors

- [Stephan de Souza](https://github.com/stephandesouza)
