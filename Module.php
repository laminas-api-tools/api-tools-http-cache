<?php

/**
 * @see       https://github.com/laminas-api-tools/api-tools-http-cache for the canonical source repository
 * @copyright https://github.com/laminas-api-tools/api-tools-http-cache/blob/master/COPYRIGHT.md
 * @license   https://github.com/laminas-api-tools/api-tools-http-cache/blob/master/LICENSE.md New BSD License
 */

namespace Laminas\ApiTools\HttpCache;

use Laminas\Mvc\MvcEvent;

class Module
{
    /**
     * {@inheritDoc}
     */
    public function getAutoloaderConfig()
    {
        // Using string class name, as this method might not get called,
        // allowing us to remove a dependency.
        return ['Laminas\Loader\StandardAutoloader' => ['namespaces' => [
            __NAMESPACE__ => __DIR__.'/src/',
        ]]];
    }

    /**
     * {@inheritDoc}
     */
    public function getConfig()
    {
        return include __DIR__.'/config/module.config.php';
    }

    public function onBootstrap(MvcEvent $e)
    {
        $app = $e->getApplication();
        $em  = $app->getEventManager();
        $sm  = $app->getServiceManager();

        $sm->get(HttpCacheListener::class)->attach($em);
    }
}
