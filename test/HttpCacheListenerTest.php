<?php
namespace ZFTest\HttpCache;

use Zend\Http\Request as HttpRequest;
use Zend\Http\Response as HttpResponse;
use Zend\Mvc\MvcEvent;
use Zend\Mvc\Router\RouteMatch;
use ZF\HttpCache\HttpCacheListener;

class HttpCacheListenerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var HttpCacheListener
     */
    protected $instance;

    protected function calculateDate($seconds)
    {
        $seconds += $_SERVER['REQUEST_TIME'];
        $date = new \DateTime("@{$seconds}", new \DateTimeZone('GMT'));

        return $date->format('D, d M Y H:i:s \G\M\T');
    }

    /**
     * @see checkStatusCode
     * @return array
     */
    public function checkStatusCodeDataProvider()
    {
        return array(
            array(array(), 200, true),
            array(array(), 404, false),
            array(array('http_codes_black_list' => array(200)), 404, true),
            array(array('http_codes_black_list' => array(404)), 404, false),
        );
    }

    /**
     * @see testOnRoute
     * @return array
     */
    public function configDataProvider()
    {
        return array(
            array(
                array('enable' => false),
                'get',
                array('controller' => 'foo'),
                array(),
            ),

            array(
                array('enable' => true),
                'get',
                array(),
                array(),
            ),

            array(
                array(
                    'enable' => true,
                    'controllers' => array(
                        'foo' => array(
                            'get' => array(
                                'expires' => array(
                                    'override' => true,
                                    'value'    => '+1 day',
                                ),
                            ),
                        ),
                    ),
                ),
                'get',
                array('controller' => 'foo'),
                array(
                    'expires' => array(
                        'override' => true,
                        'value'    => '+1 day',
                    ),
                ),
            ),

            array(
                array(
                    'enable' => true,
                    'controllers' => array(
                        'foo' => array(
                            '*' => array(
                                'expires' => array(
                                    'override' => true,
                                    'value'    => '+1 day',
                                ),
                            ),
                        ),
                    ),
                ),
                'head',
                array('controller' => 'foo'),
                array(
                    'expires' => array(
                        'override' => true,
                        'value'    => '+1 day',
                    ),
                ),
            ),

            array(
                array(
                    'enable' => true,
                    'controllers' => array(
                        '*' => array(
                            'get' => array(
                                'expires' => array(
                                    'override' => true,
                                    'value'    => '+1 day',
                                ),
                            ),
                        ),
                    ),
                ),
                'get',
                array('controller' => 'bar'),
                array(
                    'expires' => array(
                        'override' => true,
                        'value'    => '+1 day',
                    ),
                ),
            ),

            array(
                array(
                    'enable' => true,
                    'controllers' => array(
                        '*' => array(
                            '*' => array(
                                'expires' => array(
                                    'override' => true,
                                    'value'    => '+1 day',
                                ),
                            ),
                        ),
                    ),
                ),
                'head',
                array('controller' => 'baz'),
                array(
                    'expires' => array(
                        'override' => true,
                        'value'    => '+1 day',
                    ),
                ),
            ),

            array(
                array(
                    'enable' => true,
                    'controllers' => array(
                        '*' => array(
                            '*' => array(
                                'cache-control' => array(
                                    'override' => false,
                                    'value'    => 'private',
                                ),
                            ),
                        ),
                        'baz' => array(
                            'get' => array(
                                'cache-control' => array(
                                    'override' => true,
                                    'value'    => 'public',
                                ),
                            ),
                        ),
                    ),
                ),
                'head',
                array('controller' => 'baz'),
                array(
                    'cache-control' => array(
                        'override' => false,
                        'value'    => 'private',
                    ),
                ),
            ),

            array(
                array(
                    'enable' => true,
                    'controllers' => array(
                        '~[a-z]{3}~' => array(
                            '*' => array(
                                'cache-control' => array(
                                    'override' => false,
                                    'value'    => 'private',
                                ),
                            ),
                        ),
                        '*' => array(
                            'get' => array(
                                'cache-control' => array(
                                    'override' => true,
                                    'value'    => 'public',
                                ),
                            ),
                        ),
                    ),
                    'regex_delimiter' => '~',
                ),
                'head',
                array('controller' => 'baz'),
                array(
                    'cache-control' => array(
                        'override' => false,
                        'value'    => 'private',
                    ),
                ),
            ),

            array(
                array(
                    'enable' => true,
                    'controllers' => array(
                        '~[a-z]{3}~' => array(
                            '*' => array(
                                'cache-control' => array(
                                    'override' => false,
                                    'value'    => 'private',
                                ),
                            ),
                        ),
                    ),
                    'regex_delimiter' => '~',
                ),
                'head',
                array('controller' => 'baz'),
                array(
                    'cache-control' => array(
                        'override' => false,
                        'value'    => 'private',
                    ),
                ),
            ),
        );
    }

    /**
     * @see testMethodsReturnSelf
     * @return array
     */
    public function methodsReturnSelfDataProvider()
    {
        $request  = new HttpRequest();
        $response = new HttpResponse();
        $headers  = $response->getHeaders();

        return array(
            array('setCacheControl', array($headers)),
            array('setCacheConfig',  array(array())),
            array('setConfig',       array(array())),
            array('setExpires',      array($headers)),
            array('setPragma',       array($headers)),
            array('setVary',         array($headers)),
        );
    }

    /**
     * @see testOnResponse
     * @return array
     */
    public function onResponseDataProvider()
    {
        return array(
            array(
                array(
                    'enable' => true,
                    'controllers' => array(
                        'foo' => array(
                            'get' => array(
                                'cache-control' => array(
                                    'override' => true,
                                    'value'    => 'max-age=86400, must-revalidate, public',
                                ),
                                'expires' => array(
                                    'override' => true,
                                    'value'    => 'Fri, 10 Oct 2014 20:44:35 GMT',
                                ),
                                'pragma' => array(
                                    'override' => true,
                                    'value'    => 'token',
                                ),
                                'vary' => array(
                                    'override' => true,
                                    'value'    => 'accept-encoding, x-requested-with',
                                ),
                            ),
                        ),
                    ),
                ),
                'get',
                array('controller' => 'foo'),
                array(
                    'Expires'       => 'Fri, 10 Oct 2014 20:44:35 GMT',
                    'Cache-Control' => 'max-age=86400, must-revalidate, public',
                    'Pragma'        => 'token',
                    'Vary'          => 'accept-encoding, x-requested-with',
                ),
            ),
        );
    }

    /**
     * @see http://www.w3.org/Protocols/rfc2616/rfc2616-sec14.html#sec14.9
     * @see testSetCacheControl
     * @return array
     */
    public function setCacheControlDataProvider()
    {
        return array(
            array(
                array('cache-control' => array(
                    'override' => true,
                    'value'    => 'max-age=86400, must-revalidate, public',
                )),
                array(),
                array('Cache-Control' => 'max-age=86400, must-revalidate, public'),
            ),
            array(
                array('cache-control' => array(
                    'override' => true,
                    'value'    => 'max-age=86400, must-revalidate, public',
                )),
                array('Cache-Control' => 'max-age=86400, must-revalidate, public, no-cache'),
                array('Cache-Control' => 'max-age=86400, must-revalidate, public'),
            ),
            array(
                array('cache-control' => array(
                    'override' => false,
                    'value'    => 'max-age=86400, must-revalidate, public',
                )),
                array('Cache-Control' => 'max-age=86400, must-revalidate, public, no-cache'),
                array('Cache-Control' => 'max-age=86400, must-revalidate, public, no-cache'),
            ),
        );
    }

    /**
     * @see http://www.w3.org/Protocols/rfc2616/rfc2616-sec14.html#sec14.21
     * @see testSetExpires
     * @return array
     */
    public function setExpiresDataProvider()
    {
        return array(
            array(
                array('expires' => array(
                    'override' => true,
                    'value'    => '+1 day',
                )),
                array(),
                array('Expires' => $this->calculateDate(86400)),
            ),
            array(
                array('expires' => array(
                    'override' => true,
                    'value'    => '+1 day',
                )),
                array('Expires' => $this->calculateDate(0)),
                array('Expires' => $this->calculateDate(86400)),
            ),
            array(
                array('expires' => array(
                    'override' => false,
                    'value'    => '+1 day',
                )),
                array('Expires' => $this->calculateDate(0)),
                array('Expires' => $this->calculateDate(0)),
            ),

            /*
             * HTTP/1.1 clients and caches MUST treat other invalid date formats,
             * especially including the value "0", as in the past
             * (i.e., "already expired").
             */
            array(
                array('expires' => array(
                    'override' => true,
                    'value'    => 'junk-date',
                )),
                array(),
                array('Expires' => $this->calculateDate(0)),
            ),
            array(
                array('expires' => array(
                    'override' => true,
                    'value'    => 'junk-date',
                )),
                array('Date' => $this->calculateDate(10)),
                array(
                    'Date' => $this->calculateDate(10),
                    'Expires' => $this->calculateDate(10)
                ),
            ),
        );
    }

    /**
     * @see http://www.w3.org/Protocols/rfc2616/rfc2616-sec14.html#sec14.32
     * @see testSetPragma
     * @return array
     */
    public function setPragmaDataProvider()
    {
        return array(
            array(
                array('pragma' => array(
                    'override' => true,
                    'value'    => 'no-cache',
                )),
                array(),
                array('Pragma' => 'no-cache'),
            ),
            array(
                array('pragma' => array(
                    'override' => true,
                    'value'    => 'no-cache',
                )),
                array('Pragma' => 'token'),
                array('Pragma' => 'no-cache'),
            ),
            array(
                array('pragma' => array(
                    'override' => false,
                    'value'    => 'no-cache',
                )),
                array('Pragma' => 'token'),
                array('Pragma' => 'token'),
            ),
        );
    }

    /**
     * @see http://www.w3.org/Protocols/rfc2616/rfc2616-sec14.html#sec14.44
     * @see testSetVary
     * @return array
     */
    public function setVaryDataProvider()
    {
        return array(
            array(
                array('vary' => array(
                    'override' => true,
                    'value'    => 'accept-encoding, x-requested-with',
                )),
                array(),
                array('Vary' => 'accept-encoding, x-requested-with'),
            ),
            array(
                array('vary' => array(
                    'override' => true,
                    'value'    => 'accept-encoding, x-requested-with',
                )),
                array('Vary' => 'accept-encoding'),
                array('Vary' => 'accept-encoding, x-requested-with'),
            ),
            array(
                array('vary' => array(
                    'override' => false,
                    'value'    => 'accept-encoding, x-requested-with',
                )),
                array('Vary' => 'accept-encoding'),
                array('Vary' => 'accept-encoding'),
            ),
        );
    }

    public function setUp()
    {
        $this->instance = new HttpCacheListener();
    }

    /**
     * @covers \ZF\HttpCache\HttpCacheListener::checkStatusCode
     * @dataProvider checkStatusCodeDataProvider
     *
     * @param array   $config
     * @param integer $code
     * @param boolean $exResult
     */
    public function testCheckStatusCode(array $config, $code, $exResult)
    {
        $this->instance->setConfig($config);

        $response = new HttpResponse();
        $response->setStatusCode($code);

        $ret = $this->instance->checkStatusCode($response);

        $this->assertSame($exResult, $ret);
    }

    /**
     * @coversNothing
     * @dataProvider methodsReturnSelfDataProvider
     *
     * @param string $method
     * @param array  $args
     */
    public function testMethodsReturnsSelf($method, $args)
    {
        $ret = call_user_func_array(array($this->instance, $method), $args);

        $this->assertInstanceOf('\ZF\HttpCache\HttpCacheListener', $ret);
    }

    /**
     * @covers \ZF\HttpCache\HttpCacheListener::onResponse
     * @dataProvider onResponseDataProvider
     *
     * @param array  $config
     * @param string $method
     * @param array  $routeMatch
     * @param array  $exHeaders
     */
    public function testOnResponse(array $config, $method, array $routeMatch, array $exHeaders)
    {
        $request = new HttpRequest();
        $request->setMethod($method);

        $event = new MvcEvent();
        $event->setRequest($request);
        $event->setRouteMatch(new RouteMatch($routeMatch));

        $response = new HttpResponse();
        $event->setResponse($response);

        $this->instance->setConfig($config);
        $this->instance->onRoute($event);
        $this->instance->onResponse($event);

        $headers = $event->getResponse()
            ->getHeaders();

        $this->assertSame($exHeaders, $headers->toArray());
    }

    /**
     * @covers \ZF\HttpCache\HttpCacheListener::onRoute
     * @dataProvider configDataProvider
     *
     * @param array  $config
     * @param string $method
     * @param array  $routeMatch
     * @param array  $exCacheConfig
     */
    public function testOnRoute(array $config, $method, array $routeMatch, array $exCacheConfig)
    {
        $request = new HttpRequest();
        $request->setMethod($method);

        $event = new MvcEvent();
        $event->setRequest($request);
        $event->setRouteMatch(new RouteMatch($routeMatch));

        $this->instance->setConfig($config)
            ->onRoute($event);

        $cacheConfig = $this->instance->getCacheConfig();

        $this->assertSame(
            ! empty($cacheConfig),
            $this->instance->hasCacheConfig()
        );
        $this->assertSame($exCacheConfig, $cacheConfig);
    }

    /**
     * @covers \ZF\HttpCache\HttpCacheListener::setCacheControl
     * @dataProvider setCacheControlDataProvider
     *
     * @param array $cacheConfig
     * @param array $headers
     * @param array $exHeaders
     */
    public function testSetCacheControl(array $cacheConfig, array $headers, array $exHeaders)
    {
        $this->instance->setCacheConfig($cacheConfig);

        $response = new HttpResponse();
        $headers  = $response->getHeaders()
            ->addHeaders($headers);

        $this->instance->setCacheControl($headers);

        $this->assertSame($exHeaders, $headers->toArray());
    }

    /**
     * @covers \ZF\HttpCache\HttpCacheListener::setExpires
     * @dataProvider setExpiresDataProvider
     *
     * @param array $cacheConfig
     * @param array $headers
     * @param array $exHeaders
     */
    public function testSetExpires(array $cacheConfig, array $headers, array $exHeaders)
    {
        $this->instance->setCacheConfig($cacheConfig);

        $response = new HttpResponse();
        $headers  = $response->getHeaders()
            ->addHeaders($headers);

        $this->instance->setExpires($headers);

        $headers = $headers->toArray();

        $this->assertArrayHasKey('Expires', $headers);

        $date   = new \DateTime($headers['Expires']);
        $exDate = new \DateTime($exHeaders['Expires']);

        $this->assertEquals($exDate, $date, '', 2);
    }

    /**
     * @covers \ZF\HttpCache\HttpCacheListener::setPragma
     * @dataProvider setPragmaDataProvider
     *
     * @param array $cacheConfig
     * @param array $headers
     * @param array $exHeaders
     */
    public function testSetPragma(array $cacheConfig, array $headers, array $exHeaders)
    {
        $this->instance->setCacheConfig($cacheConfig);

        $response = new HttpResponse();
        $headers  = $response->getHeaders()
            ->addHeaders($headers);

        $this->instance->setPragma($headers);

        $this->assertSame($exHeaders, $headers->toArray());
    }

    /**
     * @covers \ZF\HttpCache\HttpCacheListener::setvary
     * @dataProvider setVaryDataProvider
     *
     * @param array $cacheConfig
     * @param array $headers
     * @param array $exHeaders
     */
    public function testSetVary(array $cacheConfig, array $headers, array $exHeaders)
    {
        $this->instance->setCacheConfig($cacheConfig);

        $response = new HttpResponse();
        $headers  = $response->getHeaders()
            ->addHeaders($headers);

        $this->instance->setVary($headers);

        $this->assertSame($exHeaders, $headers->toArray());
    }
}
