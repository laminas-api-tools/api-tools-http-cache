<?php

namespace Laminas\ApiTools\HttpCache;

use ArrayAccess;
use Interop\Container\ContainerInterface;
use Laminas\ServiceManager\Exception\ServiceNotCreatedException;

use function get_class;
use function gettype;
use function is_object;
use function sprintf;

class HttpCacheListenerFactory
{
    /**
     * Factory for producing an HttpCacheListener.
     *
     * Duck-types on the $container type to allow usage with
     * laminas-servicemanager versions 2.5+ and 3.0+.
     *
     * @return HttpCacheListener
     */
    public function __invoke(ContainerInterface $container)
    {
        $config = [];
        if ($container->has('config')) {
            $config = $container->get('config');
            if (isset($config['api-tools-http-cache'])) {
                $config = $config['api-tools-http-cache'];
            }
        }

        $httpCacheListener = new HttpCacheListener($this->getETagGenerator($config, $container));
        $httpCacheListener->setConfig($config);

        return $httpCacheListener;
    }

    /**
     * Returns an instance of an ETag generator.
     *
     * @param array|ArrayAccess $config
     * @return ETagGeneratorInterface
     * @throws ServiceNotCreatedException If specified etag generator does not
     *     have a corresponding service.
     * @throws ServiceNotCreatedException If specified etag generator is of an
     *     invalid type.
     */
    protected function getETagGenerator($config, ContainerInterface $container)
    {
        // Use custom generator.
        if (
            empty($config['etag']['generator'])
        ) {
            return new DefaultETagGenerator();
        }

        if (! $container->has($config['etag']['generator'])) {
            throw new ServiceNotCreatedException(sprintf(
                'ETag generator specified ("%s") does not resolve to a known service; '
                . 'please check your api-tools-http-cache.etag.generator configuration',
                $config['etag']['generator']
            ));
        }

        $eTagGenerator = $container->get($config['etag']['generator']);

        if (! $eTagGenerator instanceof ETagGeneratorInterface) {
            throw new ServiceNotCreatedException(sprintf(
                '%s requires a valid %s\ETagGeneratorInterface implementation; specified version was of type %s',
                HttpCacheListener::class,
                __NAMESPACE__,
                is_object($eTagGenerator) ? get_class($eTagGenerator) : gettype($eTagGenerator)
            ));
        }

        return $eTagGenerator;
    }
}
