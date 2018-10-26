# Changelog

## 2.1.5
2018-10-26
- Replaced the `setColumnDateFormat()` by the `setColumnDateTimeFormat` method, which now allows to format a datetime, date or time string in the wanted format, using `Carbn::parse($value)->format($format)` under the hood.
- The `setColumnDateFormat()` has been deprecated and will be removed in the `2.2` version.

## 2.1.4
2018-10-12
- Updated documentation in order to give more examples in the advanced usage.
- Replaced the `setCustomTable()` method `$columnDatabaseAlias` argument by `$customColumnTableRealAttribute` and updated the phpdoc to be more explicit about this second argument use.

## 2.1.3
2018-08-24
- `isButton()` : Now hide the button when the column has no value nor icon.
- `isLink()` : Now hide the link when the column has no value nor icon.

## 2.1.2
2018-08-24
- `setIcon()` : Now hide icon by default when the column has no value.
- `setIcon()` : Added the opportunity to show the icon even if the column has no value. 
