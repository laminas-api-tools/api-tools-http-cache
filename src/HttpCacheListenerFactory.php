<?php
namespace Laminas\ApiTools\HttpCache;

use Laminas\ServiceManager\FactoryInterface;
use Laminas\ServiceManager\ServiceLocatorInterface;

class HttpCacheListenerFactory implements FactoryInterface
{
    /**
     * @param  ServiceLocatorInterface $services
     * @return HttpCacheListener
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $config = [];
        if ($serviceLocator->has('Config')) {
            $config = $serviceLocator->get('Config');
            if (isset($config['api-tools-http-cache'])) {
                $config = $config['api-tools-http-cache'];
            }
        }

        $httpCacheListener = new HttpCacheListener();
        $httpCacheListener->setConfig($config);

        return $httpCacheListener;
    }
}
