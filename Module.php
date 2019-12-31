<?php
namespace Laminas\ApiTools\HttpCache;

class Module
{
    /**
     * {@inheritDoc}
     */
    public function getAutoloaderConfig()
    {
        return [Laminas\Loader\StandardAutoloader::class => ['namespaces' => [
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

    public function onBootstrap(\Laminas\Mvc\MvcEvent $e)
    {
        $app = $e->getApplication();
        $em  = $app->getEventManager();
        $sm  = $app->getServiceManager();

        $sm->get(HttpCacheListener::class)->attach($em);
    }
}
