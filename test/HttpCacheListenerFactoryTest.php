<?php

namespace LaminasTest\ApiTools\HttpCache;

use Interop\Container\ContainerInterface;
use Laminas\ApiTools\HttpCache\DefaultETagGenerator;
use Laminas\ApiTools\HttpCache\ETagGeneratorInterface;
use Laminas\ApiTools\HttpCache\HttpCacheListener;
use Laminas\ApiTools\HttpCache\HttpCacheListenerFactory;
use Laminas\ServiceManager\Exception\ServiceNotCreatedException;
use PHPUnit\Framework\TestCase;

class HttpCacheListenerFactoryTest extends TestCase
{
    use DeprecatedAssertionsTrait;

    public function testFactoryCreatesListenerWhenNoConfigServiceIsPresent()
    {
        $container = $this->prophesize(ContainerInterface::class);
        $container->has('config')->willReturn(false);
        $container->has('config')->willReturn(false);

        $factory  = new HttpCacheListenerFactory();
        $listener = $factory($container->reveal());
        $this->assertInstanceOf(HttpCacheListener::class, $listener);
    }

    public function testFactoryWillUseConfigServiceWhenPresentToCreateListener(): HttpCacheListener
    {
        $config = [
            'api-tools-http-cache' => [
                'enable'                => true,
                'controllers'           => [],
                'http_codes_black_list' => [201, 404],
                'regex_delimiter'       => '#',
            ],
        ];

        $container = $this->prophesize(ContainerInterface::class);
        $container->has('config')->willReturn(true);
        $container->get('config')->willReturn($config);

        $factory  = new HttpCacheListenerFactory();
        $listener = $factory($container->reveal());
        $this->assertInstanceOf(HttpCacheListener::class, $listener);
        $this->assertAttributeSame($config['api-tools-http-cache'], 'config', $listener);
        return $listener;
    }

    /**
     * @depends testFactoryWillUseConfigServiceWhenPresentToCreateListener
     */
    public function testFactoryWillSetDefaultETagGeneratorIfNoneIsSpecifiedInConfiguration(
        HttpCacheListener $listener
    ) {
        $this->assertAttributeInstanceOf(DefaultETagGenerator::class, 'eTagGenerator', $listener);
    }

    public function testFactoryWillRaiseAnExceptionIfSpecifiedGeneratorDoesNotResolveToService()
    {
        $config = [
            'api-tools-http-cache' => [
                'enable'                => true,
                'controllers'           => [],
                'http_codes_black_list' => [201, 404],
                'regex_delimiter'       => '#',
                'etag'                  => [
                    'generator' => 'not-a-valid-generator',
                ],
            ],
        ];

        $container = $this->prophesize(ContainerInterface::class);
        $container->has('config')->willReturn(true);
        $container->get('config')->willReturn($config);
        $container->has('not-a-valid-generator')->willReturn(false);

        $factory = new HttpCacheListenerFactory();

        $this->expectException(ServiceNotCreatedException::class);
        $this->expectExceptionMessage('does not resolve to a known service');
        $factory($container->reveal());
    }

    public function testFactoryWillRaiseExceptionIfSpecifiedETagGeneratorIsInvalid()
    {
        $config = [
            'api-tools-http-cache' => [
                'enable'                => true,
                'controllers'           => [],
                'http_codes_black_list' => [201, 404],
                'regex_delimiter'       => '#',
                'etag'                  => [
                    'generator' => 'not-a-valid-generator',
                ],
            ],
        ];

        $container = $this->prophesize(ContainerInterface::class);
        $container->has('config')->willReturn(true);
        $container->get('config')->willReturn($config);
        $container->has('not-a-valid-generator')->willReturn(true);
        $container->get('not-a-valid-generator')->willReturn([]);

        $factory = new HttpCacheListenerFactory();

        $this->expectException(ServiceNotCreatedException::class);
        $this->expectExceptionMessage('requires a valid');
        $factory($container->reveal());
    }

    public function testFactoryWillInjectSpecifiedETagGenerator()
    {
        $eTagGenerator = $this->prophesize(ETagGeneratorInterface::class)->reveal();

        $config = [
            'api-tools-http-cache' => [
                'enable'                => true,
                'controllers'           => [],
                'http_codes_black_list' => [201, 404],
                'regex_delimiter'       => '#',
                'etag'                  => [
                    'generator' => 'a-valid-generator',
                ],
            ],
        ];

        $container = $this->prophesize(ContainerInterface::class);
        $container->has('config')->willReturn(true);
        $container->get('config')->willReturn($config);
        $container->has('a-valid-generator')->willReturn(true);
        $container->get('a-valid-generator')->willReturn($eTagGenerator);

        $factory = new HttpCacheListenerFactory();

        $listener = $factory($container->reveal());
        $this->assertAttributeSame($eTagGenerator, 'eTagGenerator', $listener);
    }
}
