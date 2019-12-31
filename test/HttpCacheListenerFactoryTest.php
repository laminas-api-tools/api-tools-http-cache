<?php

/**
 * @see       https://github.com/laminas-api-tools/api-tools-http-cache for the canonical source repository
 * @copyright https://github.com/laminas-api-tools/api-tools-http-cache/blob/master/COPYRIGHT.md
 * @license   https://github.com/laminas-api-tools/api-tools-http-cache/blob/master/LICENSE.md New BSD License
 */

namespace LaminasTest\ApiTools\HttpCache;

use Interop\Container\ContainerInterface;
use Laminas\ApiTools\HttpCache\HttpCacheListener;
use Laminas\ApiTools\HttpCache\HttpCacheListenerFactory;
use PHPUnit_Framework_TestCase as TestCase;

class HttpCacheListenerFactoryTest extends TestCase
{
    public function testFactoryCreatesListenerWhenNoConfigServiceIsPresent()
    {
        $container = $this->prophesize(ContainerInterface::class);
        $container->has('config')->willReturn(false);

        $factory  = new HttpCacheListenerFactory();
        $listener = $factory($container->reveal());
        $this->assertInstanceOf(HttpCacheListener::class, $listener);
    }

    public function testFactoryWillUseConfigServiceWhenPresentToCreateListener()
    {
        $config = [
            'api-tools-http-cache' => [
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
        $this->assertAttributeSame($config['api-tools-http-cache'], 'config', $listener);
    }
}
