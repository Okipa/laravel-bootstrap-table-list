# Laravel Bootstrap Table List

[![Source Code](https://img.shields.io/badge/source-okipa/laravel--bootstrap--table--list-blue.svg)](https://github.com/Okipa/laravel-bootstrap-table-list)
[![Latest Version](https://img.shields.io/github/release/okipa/laravel-bootstrap-table-list.svg?style=flat-square)](https://github.com/Okipa/laravel-bootstrap-table-list/releases)
[![Total Downloads](https://img.shields.io/packagist/dt/okipa/laravel-bootstrap-table-list.svg?style=flat-square)](https://packagist.org/packages/okipa/laravel-bootstrap-table-list)
[![License: MIT](https://img.shields.io/badge/License-MIT-blue.svg)](https://opensource.org/licenses/MIT)
[![Build Status](https://scrutinizer-ci.com/g/Okipa/laravel-bootstrap-table-list/badges/build.png?b=master)](https://scrutinizer-ci.com/g/Okipa/laravel-bootstrap-table-list/build-status/master)
[![Code Coverage](https://scrutinizer-ci.com/g/Okipa/laravel-bootstrap-table-list/badges/coverage.png?b=master)](https://scrutinizer-ci.com/g/Okipa/laravel-bootstrap-table-list/?branch=master)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/Okipa/laravel-bootstrap-table-list/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/Okipa/laravel-bootstrap-table-list/?branch=master)

Because it is sometimes convenient to build a simple backoffice without sophisticated javascript treatments, Laravel Bootstrap Table List proposes a model-based and highly customizable php table list generation, that simply render your table HTML in your view, with a code-side-configuration.

![Laravel Bootstrap Table List](https://raw.githubusercontent.com/Okipa/laravel-bootstrap-table-list/master/img/laravel-bootstrap-table-list.png)

------------------------------------------------------------------------------------------------------------------------

## Before use

This V2 of this table list generator is pre-configured for **Bootstrap 4** and **Fontawesome 5**.  
However, this package is deeply configurable and it is possible to easily set it up for Bootstrap 3 and other versions of FA or other icon libraries (or not icon at all).  
If the configuration does not give enough possibilities for your customization needs, you definitely should [publish the templates and customize them](#customize-templates) in your project.

**Notes:**  
If someone is motivated to give me a functional configuration for **bootstrap 3**, I will include it in the readme. It could interest some developers.  
Anyway, a pre-configured **bootstrap 3** version of this package does exists (with less features) : [please check the v1](https://github.com/Okipa/laravel-bootstrap-table-list/tree/v1).

------------------------------------------------------------------------------------------------------------------------

## Installation

- Install the package with composer :
```bash
composer require okipa/laravel-bootstrap-table-list:^2.0
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
    ->sortByDefault()
    ->setColumnDateFormat('d/m/Y H:i:s');
```
Then, send your `$table` object in your view and render your table list :
```php
{{ $table }}
```
That's it !

### Notes :

- **Request** : No need to transmit the request to the TableList : it systematically uses the current request given by the `request()` helper to get the number of lines to show and the searching, sorting or pagination data. However, if you need to pass a particular request to the TableList, you can do it with the `setRequest()` method.
- **Column titles** : By default, the columns titles take the following value : `trans('validation.attributes.[attribute])`. You can set a custom title using the `setTitle()` method, especially when a a column is not related to a table attribute.

### Advanced usage

If you need your table list for a more advanced usage, with a multilingual project for example, here is an example of what you can do in your controller :
```php
// we instantiate a table list somewhere in the code
$table = app(TableList::class)
    ->setModel(News::class)
    ->setRequest($request)
    ->setRoutes([
        'index'      => ['alias' => 'news.index', 'parameters' => []],
        'create'     => ['alias' => 'news.create', 'parameters' => []],
        'edit'       => ['alias' => 'news.edit', 'parameters' => []],
        'destroy'    => ['alias' => 'news.destroy', 'parameters' => []],
    ])
    ->setRowsNumber(50)
    ->enableRowsNumberSelector()
    ->addQueryInstructions(function ($query) {
        $query->select('news.*')
            ->join('news_translations', 'news.id', '=', 'news_translations.news_id')
            ->where('news_translations.locale', config('app.locale'));
    })
    ->disableLines(function($model){
        return $model->id === 1 || $model->id === 2;
    }, ['disabled', 'bg-secondary'])
    ->highlightLines(function($model){
        return $model->id === 3;
    }, ['highlighted', 'bg-success']);
// we add columns
$table->addColumn('image')
    ->isCustomHtmlElement(function ($entity, $column) {
        if ($src = $entity->{$column->attribute}) {
            $imageZoomSrc = $entity->imagePath($src, $column->attribute, 'zoom');
            $imageThumbnailSrc = $entity->imagePath($src, $column->attribute, 'thumbnail');

            return "<a href='$imageZoomSrc' title='Image title' target="blank"><img class='thumbnail' src='$imageThumbnailSrc' alt='Image alt'></a>";
        }
    });
$table->addColumn('title')
    ->setCustomTable('news_translations')
    ->isSortable()
    ->isSearchable()
    ->useForDestroyConfirmation();
$table->addColumn('content')
    ->setCustomTable('news_translations')
    ->setStringLimit(30);
$table->addColumn('category_id')
    ->isButton('btn btn-default')
    ->isCustomValue(function ($entity, $column) {
        return config('news.category.' . $entity->{$column->attribute});
    });
$table->addColumn()
    ->setTitle(__('news.label.preview'))
    ->isCustomHtmlElement(function ($entity, $column) {
        $preview_route = route('news.preview', ['id' => $entity->id]);
        $preview_label = __('global.action.preview');
        return "<a class='btn btn-primary' href='$preview_route'>$preview_label</a>";
    });
$table->addColumn('released_at')
    ->isSortable()
    ->sortByDefault('desc')
    ->setColumnDateFormat('d/m/Y H:i:s');
$table->addColumn('created_at')
    ->isSortable()
    ->setColumnDateFormat('d/m/Y H:i:s');
$table->addColumn('updated_at')
    ->isSortable()
    ->setColumnDateFormat('d/m/Y H:i:s');
```

------------------------------------------------------------------------------------------------------------------------

## API

### TableList public methods

##### `public function setModel(string $tableModel): \Okipa\LaravelBootstrapTableList\TableList`
Set the model used for the table list generation (required).

##### `public function setRequest(Request $request): \Okipa\LaravelBootstrapTableList\TableList`
Set the request used for the table list generation (required).

##### `public function setRoutes(array $routes): \Okipa\LaravelBootstrapTableList\TableList`
Set the routes used for the table list generation (required) :
- Each route will be generated with the line entity id. The given extra parameters will be added for the route generation.
- The `index` route is required and must be the route that will be used to display the page that contains the table list.
- The following routes can be defined as well :
    - `create` : must be used to redirect toward the entity creation page. Displays a `Create` button under the table list if defined.
    - `edit` : must be used to redirect toward the entity edition page. Displays a `Edit` icon on each table list line if defined.
    - `destroy` : must be used to destroy a table list line. Displays a `Remove` icon on each table list line if defined.
    - Each route have to be defined with the following structure :
```php
'index' => [
    'alias' => 'news.index',
    'parameters' => [
        // set your extra parameters here or let the array empty
    ]
]
```

##### `public function setRowsNumber(int $owsNumber): TableList`
Set a custom number of rows for the table list (optional).

##### `public function enableRowsNumberSelector(): TableList`
Enables the rows number selection in the table list (optional) :
- Calling this method displays a rows number input that enable the user to choose how much rows to show.

##### `public function addQueryInstructions(Closure $queryClosure): TableList`
Set the query closure that will be used during the table list generation (optional).  
For example, you can define your joined tables here.  
The closure let you manipulate the following attribute : $query`.

##### `public function disableLines(Closure $disableLinesClosure, array $lineClass = []): TableList`
Set the disable lines closure that will be executed during the table list generation (optional).  
The optional second param let you set the class that will be applied for the disabled lines.  
By default, the `config('tablelist.value.disabled_line.class')` config value is applied.  
For example, you can disable the current logged user to prevent him being edited or deleted from the table list.  
The closure let you manipulate the following attribute : $model.

##### `public function highlightLines(Closure $highlightLinesClosure, array $lineClass = []): TableList`
Set the highlight lines closure that will executed during the table list generation (optional).  
The optional second param let you set the class that will be applied for the highlighted lines.  
By default, the `config('tablelist.value.highlighted_line.class')` config value is applied.  
The closure let you manipulate the following attribute : $model.

##### `public function addColumn(string $attribute = null) : TableList`
Add a column that will be displayed in the table list (required) :
- At least one column must be added to the table list.  
- A column can be created without attribute specification, in case of HTML element display, for example.

### TableListColumn public methods

##### `public function setTitle(string $title): TableListColumn`
Set the column title (optional).

##### `public function sortByDefault(string $direction = 'asc'): TableListColumn`
Set the default sorted column (required).

##### `public function useForDestroyConfirmation(): TableListColumn`
Use the column attribute for the destroy confirmation message generation (required) :
- At least one column must be selected for destroy confirmation if a destroy route is set.  
- This method can be called only once.

##### `public function isSortable(): TableListColumn`
Make the column sortable (optional).

##### `public function isSearchable(): TableListColumn`
Make the column searchable (optional).

##### `public function setCustomTable(string $customColumnTable): TableListColumn`
Set a custom table for the column (optional).  
Calling this method can be useful if the column attribute does not directly belong to the table list model.

##### `public function setColumnDateFormat(string $columnDateFormat): TableListColumn`
Set the format for a date (optional).  
(Carbon is used to format the date).

##### `public function isButton(string $buttonClass): TableListColumn`
Set the column button class (optional).  
The attribute is wrapped into a button.

##### `public function setIcon(string $icon): TableListColumn`
Set the icon to display before the value (optional).

##### `public function setStringLimit(int $stringLimit): TableListColumn`
Set the string value display limitation (optional).  
Shows "..." when the limit is reached.

##### `public function setIcon(string $icon, $showWithNoValue = false): TableListColumn`
Set the link url.  
You can declare the $url as a string or as a closure which will let you manipulate the following attributes : $entity, $column.  
If no $url is declared, it will be set with the column value.

##### `public function isCustomValue(Closure $customValueClosure): TableListColumn`
Set a custom value in the method closure (optional).  
The closure let you manipulate the following attributes : $entity, $column.

##### `public function isCustomHtmlElement(Closure $customHtmlEltClosure): TableListColumn`
Set the HTML element to render in the method closure (optional).  
The closure let you manipulate the following attributes : $entity, $column.

------------------------------------------------------------------------------------------------------------------------

## Configurations

To personalize the package configuration, you have to publish it first with the following script :
```bash
php artisan vendor:publish --tag=tablelist::config
```
Then, open the published package configuration file (`config/tablelist.php`) and override the default table list configuration by setting your own values.

------------------------------------------------------------------------------------------------------------------------

## Customize translations
You can customize the table list associated translation by publishing them in your project :
```bash
php artisan vendor:publish --tag=tablelist::translations
```
Once you have published them, You will find them in your `resources/lang` directory.

------------------------------------------------------------------------------------------------------------------------

## Customize templates

Publish the package blade templates file in your project :
```bash
php artisan vendor:publish --tag=tablelist::views
```
Then, change the content from the package templates in your `resources/views/vendor/tablelist` directory.

------------------------------------------------------------------------------------------------------------------------

## Testing

```bash
composer test
```

------------------------------------------------------------------------------------------------------------------------

## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

------------------------------------------------------------------------------------------------------------------------

## Contributors

- [Okipa](https://github.com/Okipa)
- [ACID-Solutions](https://github.com/ACID-Solutions)
- [Stephan de Souza](https://github.com/stephandesouza)

------------------------------------------------------------------------------------------------------------------------

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
