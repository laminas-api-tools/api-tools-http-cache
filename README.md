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

TODO

#### Key: `enable`

TODO

#### Key: `http_codes_black_list`

TODO

### System Configuration

TODO

The following configuration is provided in `config/module.config.php` to enable the module to
function:

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
