Laminas Http Cache
=============

[![Build Status](https://github.com/laminas-api-tools/api-tools-http-cache/actions/workflows/continuous-integration.yml/badge.svg)](https://github.com/laminas-api-tools/api-tools-http-cache/actions/workflows/continuous-integration.yml)

> ## 🇷🇺 Русским гражданам
>
> Мы, участники Laminas, родились и живем в разных странах. У многих из нас есть друзья, родственники и коллеги как в России, так и в Украине. Некоторые из нас родились в России. Некоторые из нас живут в России. У некоторых бабушки и дедушки сражались с фашистами во Второй мировой войне. Здесь никто не поддерживает фашизм.
>
> У одного из нас есть украинская родственница, которая спаслась из дома вместе с сыном. Поезд задержался из-за бомбежки на дороге впереди. У нас есть друзья, которые прячутся в бомбоубежищах. Мы с тревогой ждем весточки от них после воздушных налетов, которые беспорядочно наносят удары и попадают по больницам, школам, детским садам и домам. Мы не берем это из каких-либо СМИ. Мы наблюдаем это напрямую.
>
> Вы доверяете нам достаточно, чтоб использовать наши программы, и мы просим вас довериться нам вновь. Мы нуждаемся в помощи. Выходите и протестуйте против этой бесполезной войны. Остановите кровопролитие. Скажите "Нет войне!"
>
> ## 🇺🇸 To Citizens of Russia
>
> We at Laminas come from all over the world. Many of us have friends, family and colleagues in both Russia and Ukraine. Some of us were born in Russia. Some of us currently live in Russia. Some have grandparents who fought Nazis in World War II. Nobody here supports fascism.
>
> One team member has a Ukrainian relative who fled her home with her son. The train was delayed due to bombing on the road ahead. We have friends who are hiding in bomb shelters. We anxiously follow up on them after the air raids, which indiscriminately fire at hospitals, schools, kindergartens and houses. We're not taking this from any media. These are our actual experiences.
>
> You trust us enough to use our software. We ask that you trust us to say the truth on this. We need your help. Go out and protest this unnecessary war. Stop the bloodshed. Say "stop the war!"

Introduction
------------

`api-tools-http-cache` is a Laminas module for automating http-cache tasks within a Laminas
application.

Installation
------------

Run the following `composer` command:

```console
$ composer require "laminas-api-tools/api-tools-http-cache:^1.0"
```

Alternately, manually add the following to your `composer.json`, in the `require` section:

```javascript
"require": {
    "laminas-api-tools/api-tools-http-cache": "^1.0"
}
```

And then run `composer update` to ensure the module is installed.

Finally, add the module name to your project's `config/application.config.php` under the `modules`
key:


```php
return [
    /* ... */
    'modules' => [
        /* ... */
        'Laminas\ApiTools\HttpCache',
    ],
    /* ... */
];
```

Configuration
-------------

### User Configuration

**As a rule of thumb, avoid as much as possible using anonymous functions since it prevents you from caching your configuration.** 

The top-level configuration key for user configuration of this module is `api-tools-http-cache`.

The `config/module.config.php` file contains a self-explanative example of configuration.

#### Key: `controllers`

The `controllers` key is utilized for mapping any of

- a route name
- a concatenated `controller::action`
- a controller
- a regexp 
- a wildcard

Each is case sensitive, and will map one or more HTTP methods to the cache
header configuration specific to the given rule.

Example:

```php
// See `config/module.config.php` for a complete commented example
'api-tools-http-cache' => [
    /* ... */
    'controllers' => [
        '<controller>' => [
            '<http-method>'  => [
                '<cache-header-name>' => [
                    'override' => true,
                    'value'    => '<cache-header-value>',
                ],
            ],
        ],
    ],
    /* ... */
],    
```

##### Key: `<controller>` 

Either 

- a concatenation of `$controller::$action` 
- a controller name (as returned by `Laminas\Mvc\MvcEvent::getRouteMatch()->getParam('controller')`;
  the value is case-sensitive) 
- a regexp (see `<regex_delimiter>` key)
- a wildcard

A wildcard matches any unspecified controllers.

##### Key: `<http-method>` 

Either a lower cased HTTP method (`get`, `post`, etc.) (as returned by `Laminas\Http\Request::getMethod()`) or a wildcard.

A wildcard stands for all the non-specified HTTP methods.

##### Key: `<cache-header-name>` 

An HTTP cache header name (`Cache-control`, `expires`, `etag` etc.).

###### ETags

For ETags you can specify a custom generator in the configuration:

```
'etag' => [
    'override' => true,
    'generator' => 'Your\Own\ETagGenerator',
],
```

A generator example can be found in `\Laminas\ApiTools\HttpCache\DefaultETagGenerator`. 


##### Key: `<cache-header-value>`

The value for the cache header. 

##### Key: `override`

Whether to override the cache headers possibly sent by your application.

#### Key: `enable`

The `enable` key is utilized for enabling/disabling the http cache module at run time.

If you no longer need this module, rather consider removing the module from the `application.config.php` list.

**Caution: when disabled, http cache module doesn't override/remove the cache headers sent by your application.**

Example:

```php
'api-tools-http-cache' => [
    /* ... */
    'enable' => true, // Cache module is enabled.
    /* ... */
],
```

#### Key: `http_codes_black_list`

The `http_codes_black_list` is utilized to avoid caching the responses with the listed HTTP status codes.
Defaults to all others than `200`.

Example:

```php
'api-tools-http-cache' => [
    /* ... */
    'http_codes_black_list' => ['201', '304', '400', '500'], // Whatever the other configurations, the responses with these HTTP codes won't be cached.
    /* ... */
],
```

#### Key: `regex_delimiter`

This key is used to enable the evaluation of the <controller> key as a regular expression.

It must contain the delimiter of the regular expression.

If you don't want to use regular expression in your configuration set this to null to avoid inutil parsing.

Regular expressions are tested in the very order they appear in the configuration, first matching wins.

Regexp wins over wildcard.

**Caution: When this value is not empty and no litteral key corresponds to the current controller, a preg_match is used.**

Example:

```php
'api-tools-http-cache' => [
    /* ... */
    'regex_delimiter' => '~',
    /* ... */
    'controllers' => [
        '~.*~' => [ // Acts as a wildcard.
            /* ... */
        ],
    ],
    /* ... */
],
```

### System Configuration

The following configuration is provided in `config/module.config.php`:

```php
'service_manager' => [
    'factories' => [
        'Laminas\ApiTools\HttpCache\HttpCacheListener' => 'Laminas\ApiTools\HttpCache\HttpCacheListenerFactory',
    ],
],
```

Laminas Events
----------

### Listeners

#### `Laminas\ApiTools\HttpCache\HttpCacheListener`

This listener is attached to the `MvcEvent::EVENT_ROUTE` and `MvcEvent::EVENT_FINISH` events with the low priority of `-10000`.
