# Drupal Variable Mock [![Build Status](https://travis-ci.org/sikofitt/mocks-drupal-variables.svg?branch=v1.0.2)](https://travis-ci.org/sikofitt/mocks-drupal-variables)

Mocks for drupal variable functions variable_(set,get,del) for testing outside of Drupal

## Versions 
Drupal 7.x

## Usage

```composer require sikofitt/mocks-drupal-variables```

```require 'vendor/autoload.php'```

Then just use as if you were in drupal.  
```php
variable_set('myvar', 'myvalue');
variable_get('myvar'); // myvalue
variable_del('myvar');
variable_get('myvar', 'default_if_not_found'); // default_if_not_found
```
Just like drupal, every function returns null, except variable_get.
But variable_get will return null if the value is not found and no default was given.

## Requirements
PHP >=5.3

## Tests
phpunit
