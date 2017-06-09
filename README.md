# Regex
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/folour/regex/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/folour/regex/?branch=master)
[![downloads](https://poser.pugx.org/folour/regex/downloads.png)](https://packagist.org/packages/folour/regex)
[![license](https://poser.pugx.org/folour/regex/license.png)](https://packagist.org/packages/folour/regex)

Simple and useful abstraction over PHP preg_* functions for PHP 7.1

### Implemented functions
 - preg_split
 - preg_match
 - preg_match_all
 - preg_replace
 - preg_replace_callback

### Installation
```php
composer require folour/regex 'v1.0.0'
```

### Usage
```php
<?php declare(strict_types=1);

use Folour\Regex\Regex;

$content = 'Test [string], test [value]';
$re = new Regex($content);

/*
 * Replace example
 * this method returns a new instance with replaced string as content
 * Regex object returns content when converts to string
 */
$replaced = $re->replace('/test/i', 'replaced');
//Converts to string and print text 'replaced [string], replaced [value]'
echo $replaced;

//fluent replacement
$replaced = $re
    ->replace('/test/i', 'replaced')
    ->replace('/replaced/', 'double_replaced');

echo $replaced;//'double_replaced [string], double_replaced [value]

//callback replacement
$replaced = $re->replace('/\[([a-z]+)\]/i', function($matches) {
    return sprintf('[replaced_%s]', $matches[1]);
});
echo $replaced; //'Test [replaced_string], test [replaced_value]'

/*
 * find matches
 */
//first match
$m = $re->find('/\[(?P<matched>[a-z]+)\]/');
var_dump($m);
// array(
//   'matched' => 'string'
// )

//all matches
$m = $re->findAll('/\[(?P<matched>[a-z]+)\]/');
var_dump($m);
// array(
//   0 => array(
//     'matched' => 'string'
//   ),
//   1 => array(
//     'matched' => 'value'
//   )
// )

/*
 * Split string
 */
$parts = $re->split('/\,\s?/');
var_dump($parts);
// array(
//   0 => 'Test [string]',
//   1 => 'test [value]'
// )
```