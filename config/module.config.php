<?php
return array(
    'service_manager' => array(
        'factories' => array(
            'ZF\HttpCache\HttpCacheListener' => 'ZF\HttpCache\HttpCacheListenerFactory',
        )
    ),

    'zf-http-cache' => array(
    //    'controllers' => array(
    //        'home' => array( // router route name
    //            'get' => array( // Http method (wildcard '*' supported as whatever method)
    //                /*
    //                 * General HTTP caching theory: http://www.w3.org/Protocols/rfc2616/rfc2616-sec13.html
    //                 * Apache point of view: http://httpd.apache.org/docs/current/mod/mod_proxy.html
    //                 * NginX point of view: http://nginx.org/en/docs/http/ngx_http_proxy_module.html
    //                 * Varnish point of view: https://www.varnish-software.com/static/book/HTTP.html
    //                 */
    //
    //                // Cache-Control: http://www.w3.org/Protocols/rfc2616/rfc2616-sec14.html#sec14.9
    //                'cache-control' => array(
    //                    'override' => true, // Whether to override cache-control header if already present in response, false by default
    //                    'value'    => 'public, must-revalidate, max-age=86400',
    //                ),
    //
    //                // Expires: http://www.w3.org/Protocols/rfc2616/rfc2616-sec14.html#sec14.21
    //                // Cache-Control *always* overrides Expires
    //                'expires' => array(
    //                    'override' => true, // Whether to override expires header if already present in response
    //                    'value'    => '+1 day', // Can be all format accepted by DateTime::__construct(): http://www.php.net/manual/en/datetime.formats.php
    //                ),
    //
    //                // Pragma: http://www.w3.org/Protocols/rfc2616/rfc2616-sec14.html#sec14.32
    //                'pragma' => array(
    //                    'override' => true, // Whether to override pragma header if already present in response
    //                    'value'    => '',
    //                ),
    //
    //                // Vary: http://www.w3.org/Protocols/rfc2616/rfc2616-sec14.html#sec14.44
    //                'vary' => array(
    //                    'override' => true, // Whether to override vary header if already present in response
    //                    'value'    => 'accept-encoding, x-requested-with',
    //                ),
    //            ),
    //        ),
    //    ),
    //
    //    'enable' => true, // Wheter to enable http cache
    //
    //    // Never cache these HTTP status codes. Defaults to all others than 200.
    //    'http_codes_black_list' => array(),
    ),
);
