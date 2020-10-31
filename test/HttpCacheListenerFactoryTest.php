<?php

namespace LaminasTest\ApiTools\HttpCache;

use Interop\Container\ContainerInterface;
use Laminas\ApiTools\HttpCache\ETagGeneratorInterface;
use Laminas\ApiTools\HttpCache\HttpCacheListener;
use Laminas\ApiTools\HttpCache\HttpCacheListenerFactory;
use Laminas\ServiceManager\Exception\ServiceNotCreatedException;
use PHPUnit\Framework\TestCase;

class HttpCacheListenerFactoryTest extends TestCase
{
    public function testFactoryCreatesListenerWhenNoConfigServiceIsPresent()
    {
        $container = $this->createMock(ContainerInterface::class);
        $container
            ->expects($this->once())
            ->method('has')
            ->with('config')
            ->willReturn(false);

        $factory  = new HttpCacheListenerFactory();
        $listener = $factory($container);
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

        $container = $this->createMock(ContainerInterface::class);
        $container
            ->expects($this->once())
            ->method('has')
            ->with('config')
            ->willReturn(true);
        $container
            ->expects($this->once())
            ->method('get')
            ->with('config')
            ->willReturn($config);

        $factory  = new HttpCacheListenerFactory();
        $listener = $factory($container);
        $this->assertInstanceOf(HttpCacheListener::class, $listener);
        $this->assertEquals($config['api-tools-http-cache'], $listener->getConfig());
        return $listener;
    }

    /**
     * @depends testFactoryWillUseConfigServiceWhenPresentToCreateListener
     */
    public function testFactoryWillSetDefaultETagGeneratorIfNoneIsSpecifiedInConfiguration(HttpCacheListener $listener)
    {
        $this->assertInstanceOf(ETagGeneratorInterface::class, $listener->getETagGenerator());
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

        $container = $this->createMock(ContainerInterface::class);
        $container
            ->expects($this->any())
            ->method('has')
            ->withConsecutive(['config'], ['not-a-valid-generator'])
            ->willReturnOnConsecutiveCalls(true, false);

        $container
            ->expects($this->once())
            ->method('get')
            ->with('config')
            ->willReturn($config);

        $factory = new HttpCacheListenerFactory();

        $this->expectException(ServiceNotCreatedException::class);
        $this->expectExceptionMessage('does not resolve to a known service');
        $factory($container);
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

        $container = $this->createMock(ContainerInterface::class);
        $container
            ->expects($this->any())
            ->method('has')
            ->withConsecutive(['config'], ['not-a-valid-generator'])
            ->willReturnOnConsecutiveCalls(true, true);
        $container
            ->expects($this->any())
            ->method('get')
            ->withConsecutive(['config'], ['not-a-valid-generator'])
            ->willReturnOnConsecutiveCalls($config, []);

        $factory = new HttpCacheListenerFactory();

        $this->expectException(ServiceNotCreatedException::class);
        $this->expectExceptionMessage('requires a valid');
        $factory($container);
    }

    public function testFactoryWillInjectSpecifiedETagGenerator()
    {
        $eTagGenerator = $this->createMock(ETagGeneratorInterface::class);

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

        $container = $this->createMock(ContainerInterface::class);
        $container
            ->expects($this->any())
            ->method('has')
            ->withConsecutive(['config'], ['a-valid-generator'])
            ->willReturnOnConsecutiveCalls(true, true);
        $container
            ->expects($this->any())
            ->method('get')
            ->withConsecutive(['config'], ['a-valid-generator'])
            ->willReturnOnConsecutiveCalls($config, $eTagGenerator);

        $factory = new HttpCacheListenerFactory();

        $listener = $factory($container);
        $this->assertSame($eTagGenerator, $listener->getETagGenerator());
    }
}
