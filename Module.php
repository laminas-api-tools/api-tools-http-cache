<?php
namespace ZF\HttpCache;

use Zend\Loader\StandardAutoloader;

class Module
{
    /**
     * {@inheritDoc}
     */
    public function getAutoloaderConfig()
    {
        return [StandardAutoloader::class => ['namespaces' => [
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

    public function onBootstrap(\Zend\Mvc\MvcEvent $e)
    {
        $app = $e->getApplication();
        $em  = $app->getEventManager();
        $sm  = $app->getServiceManager();

        $sm->get(HttpCacheListener::class)->attach($em);
    }
}
