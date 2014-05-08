ZF Http Cache
=============

[![Build Status](https://travis-ci.org/corentin-larose/zf-http-cache.png)](https://travis-ci.org/corentin-larose/zf-http-cache)

Introduction
------------

`zf-http-cache` is a ZF2 module for automating http-cache tasks within a Zend Framework 2
application.

Installation
------------

Run the following `composer` command:

```console
$ composer require "corentin-larose/zf-http-cache:dev-master"
```

Alternately, manually add the following to your `composer.json`, in the `require` section:

```javascript
"require": {
    "corentin-larose/zf-http-cache": "dev-master"
}
```

And then run `composer update` to ensure the module is installed.

Finally, add the module name to your project's `config/application.config.php` under the `modules`
key:


```php
return array(
    /* ... */
    'modules' => array(
        /* ... */
        'ZF\HttpCache',
    ),
    /* ... */
);
```

Configuration
-------------

### User Configuration

The top-level configuration key for user configuration of this module is `zf-http-cache`.

The `config/module.config.php` file contains a self-explanative example of configuration.

#### Key: `controllers`

The `controllers` key is utilized for mapping a combination of a controller and a HTTP method (see below) to a cache header configuration.

Example:

```php
// See the `config/application.config.php` for a complete commented example
'zf-http-cache' => array(
    /* ... */
    'controllers' => array(
        '<controller>' => array(
            '<http-method>'  => array(
                '<cache-header-name>' => array(
                    'override' => true,
                    'value'    => '<cache-header-value>',
                ),
            ),
        ),
    ),
    /* ... */
),    
```

##### Key: `<controller>` 

Either a controller name (as returned by `Zend\Mvc\MvcEvent::getRouteMatch()->getParam('controller')`, case-sensitive) or a wildcard.
A wildcard stands for all the non-specified controllers.

##### Key: `<http-method>` 

Either a lower cased HTTP method (`get`, `post`, etc.) (as returned by `Zend\Http\Request::getMethod()`) or a wildcard.
A wildcard stands for all the non-specified HTTP methods.

##### Key: `<cache-header-name>` 

A http cache header name (`Cache-control`, `Expires`, etc.).

##### Key: `<cache-header-value>`

The value for the cache header. 
As a rule of thumb, avoid as much as possible using anonymous functions since it prevents you from caching your configuration. 

##### Key: `override`

Whether to override the cache headers possibly sent by your application.

#### Key: `enable`

The `enable` key is utilized for enabling/disabling the http cache module.
**Caution: when disabled, http cache module doesn't override/remove the cache headers sent by your application.**

Example:

```php
'zf-http-cache' => array(
    /* ... */
    'enable' => true, // Cache module is enabled.
    /* ... */
),    
```

#### Key: `http_codes_black_list`

The `http_codes_black_list` is utilized to avoid caching the responses with the listed HTTP status codes.
Defaults to all others than `200`.

Example:

```php
'zf-http-cache' => array(
    /* ... */
    'http_codes_black_list' => array('201', '304', '400', '500'), // Whatever the other configurations, the responses with these HTTP codes won't be cached.
    /* ... */
),
```

### System Configuration

The following configuration is provided in `config/module.config.php`:

```php
'service_manager' => array(
    'factories' => array(
        'ZF\HttpCache\HttpCacheListener' => 'ZF\HttpCache\HttpCacheListenerFactory',
    )
),
```

ZF2 Events
----------

### Listeners

#### `ZF\HttpCache\HttpCacheListener`

This listener is attached to the `MvcEvent::EVENT_ROUTE` and `MvcEvent::EVENT_FINISH` events with priority `-1000`.
