<?php
namespace Laminas\ApiTools\HttpCache;

class Module
{
    /**
     * {@inheritDoc}
     */
    public function getAutoloaderConfig()
    {
        return array('Laminas\Loader\StandardAutoloader' => array('namespaces' => array(
            __NAMESPACE__ => __DIR__.'/src/',
        )));
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

        $em->attachAggregate($sm->get('Laminas\ApiTools\HttpCache\HttpCacheListener'));
    }
}
