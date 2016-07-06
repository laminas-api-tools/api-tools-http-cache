<?php
/**
 * @license   http://opensource.org/licenses/BSD-3-Clause BSD-3-Clause
 * @copyright Copyright (c) 2015 Zend Technologies USA Inc. (http://www.zend.com)
 */

namespace ZFTest\HttpCache;

use Interop\Container\ContainerInterface;
use PHPUnit_Framework_TestCase as TestCase;
use Zend\ServiceManager\Exception\ServiceNotCreatedException;
use ZF\HttpCache\DefaultETagGenerator;
use ZF\HttpCache\ETagGeneratorInterface;
use ZF\HttpCache\HttpCacheListener;
use ZF\HttpCache\HttpCacheListenerFactory;

class HttpCacheListenerFactoryTest extends TestCase
{
    public function testFactoryCreatesListenerWhenNoConfigServiceIsPresent()
    {
        $container = $this->prophesize(ContainerInterface::class);
        $container->has('config')->willReturn(false);
        $container->has('config')->willReturn(false);

        $factory  = new HttpCacheListenerFactory();
        $listener = $factory($container->reveal());
        $this->assertInstanceOf(HttpCacheListener::class, $listener);
    }

    public function testFactoryWillUseConfigServiceWhenPresentToCreateListener()
    {
        $config = [
            'zf-http-cache' => [
                'enable'                => true,
                'controllers'           => [],
                'http_codes_black_list' => [ 201, 404 ],
                'regex_delimiter'       => '#',
            ],
        ];

        $container = $this->prophesize(ContainerInterface::class);
        $container->has('config')->willReturn(true);
        $container->get('config')->willReturn($config);

        $factory  = new HttpCacheListenerFactory();
        $listener = $factory($container->reveal());
        $this->assertInstanceOf(HttpCacheListener::class, $listener);
        $this->assertAttributeSame($config['zf-http-cache'], 'config', $listener);
        return $listener;
    }

    /**
     * @depends testFactoryWillUseConfigServiceWhenPresentToCreateListener
     */
    public function testFactoryWillSetDefaultETagGeneratorIfNoneIsSpecifiedInConfiguration($listener)
    {
        $this->assertAttributeInstanceOf(DefaultETagGenerator::class, 'eTagGenerator', $listener);
    }

    public function testFactoryWillRaiseAnExceptionIfSpecifiedGeneratorDoesNotResolveToService()
    {
        $config = [
            'zf-http-cache' => [
                'enable'                => true,
                'controllers'           => [],
                'http_codes_black_list' => [ 201, 404 ],
                'regex_delimiter'       => '#',
                'etag' => [
                    'generator' => 'not-a-valid-generator',
                ],
            ],
        ];

        $container = $this->prophesize(ContainerInterface::class);
        $container->has('config')->willReturn(true);
        $container->get('config')->willReturn($config);
        $container->has('not-a-valid-generator')->willReturn(false);

        $factory  = new HttpCacheListenerFactory();

        $this->setExpectedException(ServiceNotCreatedException::class, 'does not resolve to a known service');
        $listener = $factory($container->reveal());
    }

    public function testFactoryWillRaiseExceptionIfSpecifiedETagGeneratorIsInvalid()
    {
        $config = [
            'zf-http-cache' => [
                'enable'                => true,
                'controllers'           => [],
                'http_codes_black_list' => [ 201, 404 ],
                'regex_delimiter'       => '#',
                'etag' => [
                    'generator' => 'not-a-valid-generator',
                ],
            ],
        ];

        $container = $this->prophesize(ContainerInterface::class);
        $container->has('config')->willReturn(true);
        $container->get('config')->willReturn($config);
        $container->has('not-a-valid-generator')->willReturn(true);
        $container->get('not-a-valid-generator')->willReturn([]);

        $factory  = new HttpCacheListenerFactory();

        $this->setExpectedException(ServiceNotCreatedException::class, 'requires a valid');
        $listener = $factory($container->reveal());
    }

    public function testFactoryWillInjectSpecifiedETagGenerator()
    {
        $eTagGenerator = $this->prophesize(ETagGeneratorInterface::class)->reveal();

        $config = [
            'zf-http-cache' => [
                'enable'                => true,
                'controllers'           => [],
                'http_codes_black_list' => [ 201, 404 ],
                'regex_delimiter'       => '#',
                'etag' => [
                    'generator' => 'a-valid-generator',
                ],
            ],
        ];

        $container = $this->prophesize(ContainerInterface::class);
        $container->has('config')->willReturn(true);
        $container->get('config')->willReturn($config);
        $container->has('a-valid-generator')->willReturn(true);
        $container->get('a-valid-generator')->willReturn($eTagGenerator);

        $factory  = new HttpCacheListenerFactory();

        $listener = $factory($container->reveal());
        $this->assertAttributeSame($eTagGenerator, 'eTagGenerator', $listener);
    }
}
