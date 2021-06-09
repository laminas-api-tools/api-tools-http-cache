<?php

namespace Laminas\ApiTools\HttpCache;

use Laminas\Mvc\MvcEvent;

class Module
{
    /**
     * Provide configuration for this module.
     *
     * @return array
     */
    public function getConfig()
    {
        return include __DIR__ . '/../config/module.config.php';
    }

    /**
     * Listen to bootstrap event.
     *
     * Attaches the HttpCacheListener to the application event manager.
     *
     * @return void
     */
    public function onBootstrap(MvcEvent $e)
    {
        $app = $e->getApplication();
        $em  = $app->getEventManager();
        $sm  = $app->getServiceManager();

        $sm->get(HttpCacheListener::class)->attach($em);
    }
}
