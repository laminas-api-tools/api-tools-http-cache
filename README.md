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

#### Key: `controllers`

The `controllers` key is utilized for mapping a combination of a route and a HTTP method (see below) to a cache configuration.

##### Sub-key: `*|<route name>`

Either a route name (as returned by `Zend\Mvc\MvcEvent::getRouteMatch()`, case-sensitive) or a wildcard.
A wildcard stands for all the non-specified routes.

###### Sub-sub-key: `<http method>`

Either a HTTP method (as returned by `Zend\Http\Request::getMethod()`) in lower case or a wildcard.
A wildcard stands for all the non-specified HTTP methods.

#### Key: `enable`

The `enable` key is utilized for enabling/disabling the http cache module.
** Caution: when disabled, http cache module doesn't override/remove the cache headers sent by your application. **

Example:

```php
'enable' => true, // Cache module is enabled.
```
#### Key: `http_codes_black_list`

The `http_codes_black_list` is utilized to avoid caching the responses with the listed HTTP status codes.
Defaults to all others than `200 OK`.

Example:

```php
'http_codes_black_list' => array('201', '304', '400', '500'), // Whatever the other configurations, the responses with these codes won't be cached.
```

### System Configuration

TODO

The following configuration is provided in `config/module.config.php` to enable the module to function:

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
