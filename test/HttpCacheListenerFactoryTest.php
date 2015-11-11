<?php
/**
 * @license   http://opensource.org/licenses/BSD-3-Clause BSD-3-Clause
 * @copyright Copyright (c) 2015 Zend Technologies USA Inc. (http://www.zend.com)
 */

namespace ZFTest\HttpCache;

use Interop\Container\ContainerInterface;
use PHPUnit_Framework_TestCase as TestCase;
use ZF\HttpCache\HttpCacheListener;
use ZF\HttpCache\HttpCacheListenerFactory;

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
    }
}
