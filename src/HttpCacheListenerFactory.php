<?php

/**
 * @see       https://github.com/laminas-api-tools/api-tools-http-cache for the canonical source repository
 * @copyright https://github.com/laminas-api-tools/api-tools-http-cache/blob/master/COPYRIGHT.md
 * @license   https://github.com/laminas-api-tools/api-tools-http-cache/blob/master/LICENSE.md New BSD License
 */

namespace Laminas\ApiTools\HttpCache;

class HttpCacheListenerFactory
{
    /**
     * Factory for producing an HttpCacheListener.
     *
     * Duck-types on the $container type to allow usage with
     * laminas-servicemanager versions 2.5+ and 3.0+.
     *
     * @param  \Interop\Container\ContainerInterface|\Laminas\ServiceManagerServiceLocatorInterface $container
     * @return HttpCacheListener
     */
    public function __invoke($container)
    {
        $config = [];
        if ($container->has('config')) {
            $config = $container->get('config');
            if (isset($config['api-tools-http-cache'])) {
                $config = $config['api-tools-http-cache'];
            }
        }

        $httpCacheListener = new HttpCacheListener();
        $httpCacheListener->setConfig($config);

        return $httpCacheListener;
    }
}
