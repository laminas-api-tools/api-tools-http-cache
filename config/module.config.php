<?php
return array(
    'service_manager' => array(
        'factories' => array(
            'ZF\HttpCache\HttpCacheListener' => 'ZF\HttpCache\Factory\HttpCacheListenerFactory',
        )
    ),

    'zf-http-cache' => array(
        'controllers' => array(
            '' => array(
                'max-age' => array(
                    'override' => true,  // Whether to override max-age header if already present in response
                    'value'    => 86400,
                ),
                's-maxage' => array(
                    'override' => true,  // Whether to override s-maxage header if already present in response
                    'value'    => 86400,
                ),
            ),
        ),

        'enable' => true, // Wheter to enable http cache

        'http_codes_black_list'   => array(), // See HttpCacheListener::$defaultHttpCodesBlackList for default list
        'http_codes_white_list'   => array(), // See HttpCacheListener::$defaultHttpCodesWitheList for default list

        'http_methods_black_list' => array(), // See HttpCacheListener::$defaultHttpMethodsBlackList for default list
        'http_methods_white_list' => array(), // See HttpCacheListener::$defaultHttpMethodsWitheList for default list
    ),
);
