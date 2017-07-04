# Laravel Bootstrap Table List

A Laravel entities table list generator, that simply builds your table HTML directly from your controller.

![Laravel Bootstrap Table List](https://raw.githubusercontent.com/Okipa/laravel-bootstrap-table-list/master/img/laravel-bootstrap-table-list.png)

------------------------------------------------------------------------------------------------------------------------

### Important : requirements
 This package requires the following packages to work properly :
 - [laravel-toggle-switch-button](https://github.com/Okipa/laravel-toggle-switch-button) package (installation with `composer`).
 - [Bootstrap notify](http://bootstrap-notify.remabledesigns.com/) (installation with `bower`) : to make sure to get advantage of the package notifications, make sure that you have a JavaScript `notify()` method, like the following example :
 ```
 function notify(messages, type) {
   if (messages) {
     // we set the icon to show
     var icon;
     switch(type){
       case 'success':
         icon = 'your_success_icon_html';
         break;
       case 'danger':
         icon = 'your_error_icon_html';
         break;
       [...]
       default:
         icon = 'your_info_icon_html';
         break;
     }

     _.each(messages, function (message) {
       $.notify({
         // options
         icon: icon,
         message: message
       }, {
         // settings
         type: type,
         delay: 6000,
         allow_dismiss: false,
         showProgressbar: true,
         animate: {
           enter: 'animated bounceInDown',
           exit: 'animated bounceOutUp'
         }
       });
     });
   }
 }
 ```
 If you do not have a `notify()` method, no notifications will be shown.

------------------------------------------------------------------------------------------------------------------------

## Installation
1. Install the package with composer :
```
composer require okipa/laravel-bootstrap-table-list
```
2. Add the package service provider in the `register()` method from your `app/Providers/AppServiceProvider.php` :
```
// laravel bootstrap table list
// https://github.com/Ok`ipa/laravel-bootstrap-table-list
$this->app->register(Okipa\LaravelBootstrapTableList\TableListServiceProvider::class);
```
3. Load the package `CSS` or `SASS` file from the `[path/to/composer/vendor]/okipa/laravel-bootstrap-table-list/styles` directory to your project.
3. Load the package `javascript` file from the `[path/to/composer/vendor]/okipa/laravel-bootstrap-table-list/scripts` directory to your project.

------------------------------------------------------------------------------------------------------------------------
## Package usage

### Basic usage
In your controller, simply call the package like the following example to generate your table list :
```
// we instantiate a table list in the news controller
$table = app(TableList::class)
    ->setModel(News::class)
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
```
{!! $table->render() !!}
```
That's it !

### Advanced usage
If you need your table list for a more advanced usage, with a multilingual project for example, here is an example of what you can do in your controller :
```
// we instantiate a table list in the news controller
$table = app(TableList::class)
    ->setModel(News::class)
    ->setRequest($request)
    ->setRoutes([
        'index'      => ['alias' => 'news.index', 'parameters' => []],
        'create'     => ['alias' => 'news.create', 'parameters' => []],
        'edit'       => ['alias' => 'news.edit', 'parameters' => []],
        'destroy'    => ['alias' => 'news.destroy', 'parameters' => []],
        'activation' => ['alias' => 'news.activate', 'parameters' => []],
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
    ->isImage(function ($entity, $column) {
        if ($entity->{$column->attribute}) {
            return $entity->imagePath();
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
    ->isCustomHTMLElement(function ($entity, $column) {
        $preview_route = route('news.preview', ['id' => $entity->id]);
        $preview_label = trans('global.action.preview');
        return "<a class='btn btn-primary' href='$preview_route'>$preview_label</a>";
    });
$table->addColumn('released_at')
    ->setTitle(trans('news.label.released_at'))
    ->isSortable()
    ->sortByDefault('desc')
    ->formatDate('d/m/Y H:i:s');
$table->addColumn('active')
    ->setTitle(trans('news.label.activation'))
    ->isSortable()
    ->isActivationToggle();
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

### setModel($model)
| Parameter | Type | Required/Optional | Description |
|-----------|-----------|-----------|-----------|
| `$model` | `String` | Required | Set the model used for the table list generation |

### setRequest($request)
| Parameter | Type | Required/Optional | Description |
|-----------|-----------|-----------|-----------|
| `$request` | `Illuminate\Http\Request` | Required | Set the request used for the table list generation |

### setRoutes($routes)
| Parameter | Type | Required/Optional | Description |
|-----------|-----------|-----------|-----------|
| `$routes` | `Array` | Required | Set the routes used for the table list generation |

Each route have to be defined with the following structure :
```
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
- `activation` : must be used to activate / deactivate a table list line. Displays a `Activation` toggle switch on each table list line if defined.  

### setRowsNumber($rows_number)
| Parameter | Type | Required/Optional | Description |
|-----------|-----------|-----------|-----------|
| `$rows_number` | `Integer` | Optional | Set a custom number of rows for the table list |

### enableRowsNumberSelector()
| Parameter | Type | Required/Optional | Description |
|-----------|-----------|-----------|-----------|
| | | Optional | Enables the rows number selection in the table list |

**Note :** calling this method displays a rows number input that enable the user to choose how much rows to show.

### addQueryInstructions($rows_$query_closure)
| Parameter | Type | Required/Optional | Description |
|-----------|-----------|-----------|-----------|
| `$query_closure` | `Closure` | Optional | Set the query closure that will be used during the table list generation. For example, you can define your joined tables here (check usage example above) |

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

### setCustomTable($custom_table)
| Parameter | Type | Required/Optional | Description |
|-----------|-----------|-----------|-----------|
| `$custom_table` | `String` | Optional | Set a custom table for the column. Calling this method can be useful if the column attribute does not directly belong to the table list model |

### formatDate($date_format)
| Parameter | Type | Required/Optional | Description |
|-----------|-----------|-----------|-----------|
| `$date_format` | `String` | Optional | Set the format for a date (Carbon is used for formatting the date) |

### isButton($button_class)
| Parameter | Type | Required/Optional | Description |
|-----------|-----------|-----------|-----------|
| `$button_class` | `String` | Optional | Set the column button class. The attribute is wrapped into a button |

### isImage($image_path_closure)
| Parameter | Type | Required/Optional | Description |
|-----------|-----------|-----------|-----------|
| `$image_path_closure` | `Closure` | Optional | Set the image path in the method closure |

**Note :** use the `$entity` and `$column` parameters in the closure to return the relative path of your image.

### setStringLimit($string_limit)
| Parameter | Type | Required/Optional | Description |
|-----------|-----------|-----------|-----------|
| `$string_limit` | `Integer` | Optional | Set the string value display limitation. Shows `...` when the limit is reached |

### isActivationToggle()
| Parameter | Type | Required/Optional | Description |
|-----------|-----------|-----------|-----------|
|  |  | Optional | Displays an activation toggle to activate / deactivate the entity |

**Notes :**
- the `activation` route must be defined and the activation toggle can be called only once.
- the controller treatment must send back the following json response :
```
{
    "active":true (or false),
    "messages":[
        "your first message",
        "your second message",
        ...
    ]
}
```
In case of error (exception, permission, ...), the `active` value will be used to set back the toggle to its database value.
The `messages` array will be used to display your activation notifications messages.

### isLink($link_closure)
| Parameter | Type | Required/Optional | Description |
|-----------|-----------|-----------|-----------|
| `$link_closure` | `Closure` | Optional | Set the link in the method closure |

### isCustomValue($custom_value_closure)
| Parameter | Type | Required/Optional | Description |
|-----------|-----------|-----------|-----------|
| `$custom_value_closure` | `Closure` | Optional | Set a custom value in the method closure |

### isCustomHTMLElement($custom_html_element_closure)
| Parameter | Type | Required/Optional | Description |
|-----------|-----------|-----------|-----------|
| `$custom_html_element_closure` | `Closure` | Optional | Set a custom HTML element in the method closure |

------------------------------------------------------------------------------------------------------------------------

## Configuration
To personalize the package configuration, you have to publish it first with the following script :
```
php artisan vendor:publish --tag=tablelist::config
```
Then, open the published package configuration file (`config/tablelist.php`) and override the default table list configuration by setting your own values for the following items :
- default number of displayed rows  
- more to come ...

------------------------------------------------------------------------------------------------------------------------

## Customize styles
Simply override the `CSS` or `SASS` to customize the package styles.  
**Note :** If you use `SASS`, check how to customize the laravel-toggle-switch-button [here](https://github.com/Okipa/laravel-toggle-switch-button#customize-styles).

------------------------------------------------------------------------------------------------------------------------

## Customize scripts
Copy the content of the javascript file from the `[path/to/composer/vendor]/okipa/tablelist/scripts` directory and customize it into your own javascript file.
Do not forget the remove the package scripts load if you do so.

------------------------------------------------------------------------------------------------------------------------

## Customize templates
Publish the package blade templates file in your project :
```
php artisan vendor:publish --tag=tablelist::views
```
Then, change the content from the package templates in your `resources/views/vendor/tablelist` directory.
