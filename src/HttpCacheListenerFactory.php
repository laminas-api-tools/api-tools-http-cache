<?php
/**
 * @license   http://opensource.org/licenses/BSD-3-Clause BSD-3-Clause
 * @copyright Copyright (c) 2015-2016 Zend Technologies USA Inc. (http://www.zend.com)
 */

namespace ZF\HttpCache;

use Interop\Container\ContainerInterface;
use Zend\ServiceManager\Exception\ServiceNotCreatedException;

class HttpCacheListenerFactory
{
    /**
     * Factory for producing an HttpCacheListener.
     *
     * Duck-types on the $container type to allow usage with
     * zend-servicemanager versions 2.5+ and 3.0+.
     *
     * @param  ContainerInterface $container
     * @return HttpCacheListener
     */
    public function __invoke(ContainerInterface $container)
    {
        $config = [];
        if ($container->has('config')) {
            $config = $container->get('config');
            if (isset($config['zf-http-cache'])) {
                $config = $config['zf-http-cache'];
            }
        }

        $httpCacheListener = new HttpCacheListener($this->getETagGenerator($config, $container));
        $httpCacheListener->setConfig($config);

        return $httpCacheListener;
    }

    /**
     * Returns an instance of an ETag generator.
     *
     * @param array|\ArrayAccess $config
     * @param ContainerInterface $container
     * @return ETagGeneratorInterface
     * @throws ServiceNotCreatedException If specified etag generator does not
     *     have a corresponding service.
     * @throws ServiceNotCreatedException If specified etag generator is of an
     *     invalid type.
     */
    protected function getETagGenerator($config, ContainerInterface $container)
    {
        // Use custom generator.
        if (empty($config['etag']['generator'])
        ) {
            return new DefaultETagGenerator();
        }

        if (! $container->has($config['etag']['generator'])) {
            throw new ServiceNotCreatedException(sprintf(
                'ETag generator specified ("%s") does not resolve to a known service; '
                . 'please check your zf-http-cache.etag.generator configuration',
                $config['etag']['generator']
            ));
        }

        $eTagGenerator = $container->get($config['etag']['generator']);

        if (! $eTagGenerator instanceof ETagGeneratorInterface) {
            throw new ServiceNotCreatedException(sprintf(
                '%s requires a valid %s\ETagGeneratorInterface implementation; specified version was of type %s',
                HttpCacheListener::class,
                __NAMESPACE__,
                (is_object($eTagGenerator) ? get_class($eTagGenerator) : gettype($eTagGenerator))
            ));
        }

        return $eTagGenerator;
    }
}
