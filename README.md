# Laravel Bootstrap Table List

[![Source Code](https://img.shields.io/badge/source-okipa/laravel--bootstrap--table--list-blue.svg)](https://github.com/Okipa/laravel-bootstrap-table-list)
[![Latest Version](https://img.shields.io/github/release/okipa/laravel-bootstrap-table-list.svg?style=flat-square)](https://github.com/ACID-Solutions/input-sanitizer/releases)
[![Total Downloads](https://img.shields.io/packagist/dt/okipa/laravel-bootstrap-table-list.svg?style=flat-square)](https://packagist.org/packages/ACID-Solutions/input-sanitizer)
[![License: MIT](https://img.shields.io/badge/License-MIT-blue.svg)](https://opensource.org/licenses/MIT)

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
    ->setTableModel(News::class)
    ->setRequest($request)
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
    ->formatDate('d/m/Y H:i:s');
$table->addColumn('updated_at')
    ->setTitle('Update date')
    ->isSortable()
    ->formatDate('d/m/Y H:i:s');
```
Then, send your `$table` object in your view and render your table list :
```php
{!! $table->render() !!}
```
That's it !

### Advanced usage
If you need your table list for a more advanced usage, with a multilingual project for example, here is an example of what you can do in your controller :
```php
// we instantiate a table list in the news controller
$table = app(TableList::class)
    ->setTableModel(News::class)
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
    ->formatDate('d/m/Y H:i:s');
$table->addColumn('created_at')
    ->setTitle(trans('news.label.created_at'))
    ->isSortable()
    ->formatDate('d/m/Y H:i:s');
$table->addColumn('updated_at')
    ->setTitle(trans('news.label.updated_at'))
    ->isSortable()
    ->formatDate('d/m/Y H:i:s');
```

------------------------------------------------------------------------------------------------------------------------

## TableList object API

### setTableModel($tableModel)
| Parameter | Type | Required/Optional | Description |
|-----------|-----------|-----------|-----------|
| `$tableModel` | `String` | Required | Set the model used for the table list generation |

### setRequest($request)
| Parameter | Type | Required/Optional | Description |
|-----------|-----------|-----------|-----------|
| `$request` | `Illuminate\Http\Request` | Required | Set the request used for the table list generation |

### setRoutes($routes)
| Parameter | Type | Required/Optional | Description |
|-----------|-----------|-----------|-----------|
| `$routes` | `Array` | Required | Set the routes used for the table list generation |

Each route have to be defined with the following structure :
```php
'index' => [
    'alias' => 'news.index',
    'parameters' => [
        // set your extra parameters here or let the array empty 
    ]
]
```
**Note :** each route will be generated with the line entity id. The given extra parameters will be added for the route generation.  

The `index` route is required and must be the route that will be used to display the page that contains the table list.  
The following routes can be defined as well :
- `create` : must be used to redirect toward the entity creation page. Displays a `Create` button under the table list if defined.  
- `edit` : must be used to redirect toward the entity edition page. Displays a `Edit` icon on each table list line if defined.  
- `destroy` : must be used to destroy a table list line. Displays a `Remove` icon on each table list line if defined.

### setRowsNumber($rows_number)
| Parameter | Type | Required/Optional | Description |
|-----------|-----------|-----------|-----------|
| `$rows_number` | `Integer` | Optional | Set a custom number of rows for the table list |

### enableRowsNumberSelector()
| Parameter | Type | Required/Optional | Description |
|-----------|-----------|-----------|-----------|
| | | Optional | Enables the rows number selection in the table list |

**Note :** calling this method displays a rows number input that enable the user to choose how much rows to show.

### addQueryInstructions($queryClosure)
| Parameter | Type | Required/Optional | Description |
|-----------|-----------|-----------|-----------|
| `$queryClosure` | `Closure` | Optional | Set the query closure that will be used during the table list generation. For example, you can define your joined tables here (check usage example above) |

**Note :** use the `$query` parameter in the closure to add your custom instructions.

### addColumn($attribute)
| Parameter | Type | Required/Optional | Description |
|-----------|-----------|-----------|-----------|
| `$attribute` | `String` or `null` | Required | Add a column that will be displayed in the table list |

**Notes :**
- at least one column must be added to the table list.  
- a column can be created without attribute specification, in case of HTML element display, for example.

------------------------------------------------------------------------------------------------------------------------

## TableListColumn object API

### setTitle($title)
| Parameter | Type | Required/Optional | Description |
|-----------|-----------|-----------|-----------|
| `$title` | `String` | Required | Set the column title |

### sortByDefault($direction)
| Parameter | Type | Required/Optional | Description |
|-----------|-----------|-----------|-----------|
| `$direction` | `String` | Required | Set the default sorted column |

**Note :** `asc` or `desc` are the only accepted values.

### useForDestroyConfirmation()
| Parameter | Type | Required/Optional | Description |
|-----------|-----------|-----------|-----------|
|  | | Required | Use the column attribute for the destroy confirmation message generation |

**Note :** this method has to be called on one column and can be called only once.

### isSortable()
| Parameter | Type | Required/Optional | Description |
|-----------|-----------|-----------|-----------|
|  |  | Optional | Make the column sortable |

### isSearchable()
| Parameter | Type | Required/Optional | Description |
|-----------|-----------|-----------|-----------|
|  |  | Optional | Make the column searchable |

### setCustomTable($customColumnTable)
| Parameter | Type | Required/Optional | Description |
|-----------|-----------|-----------|-----------|
| `$customColumnTable` | `String` | Optional | Set a custom table for the column. Calling this method can be useful if the column attribute does not directly belong to the table list model |

### formatDate($dateFormat)
| Parameter | Type | Required/Optional | Description |
|-----------|-----------|-----------|-----------|
| `$dateFormat` | `String` | Optional | Set the format for a date (Carbon is used for formatting the date) |

### isButton($buttonClass)
| Parameter | Type | Required/Optional | Description |
|-----------|-----------|-----------|-----------|
| `$buttonClass` | `String` | Optional | Set the column button class. The attribute is wrapped into a button |

### setStringLimit($stringLimit)
| Parameter | Type | Required/Optional | Description |
|-----------|-----------|-----------|-----------|
| `$stringLimit` | `Integer` | Optional | Set the string value display limitation. Shows `...` when the limit is reached |

### isLink($linkClosure)
| Parameter | Type | Required/Optional | Description |
|-----------|-----------|-----------|-----------|
| `$linkClosure` | `Closure` | Optional | Set the link in the method closure |

### isCustomValue($customValueClosure)
| Parameter | Type | Required/Optional | Description |
|-----------|-----------|-----------|-----------|
| `$customValueClosure` | `Closure` | Optional | Set a custom value in the method closure |

### isCustomHtmlElement($customHtmlElementClosure)
| Parameter | Type | Required/Optional | Description |
|-----------|-----------|-----------|-----------|
| `$customHtmlElementClosure` | `Closure` | Optional | Set a custom HTML element in the method closure |

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
