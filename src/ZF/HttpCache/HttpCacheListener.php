<?php
namespace ZF\HttpCache;

use Zend\EventManager\ListenerAggregateInterface;
use Zend\EventManager\ListenerAggregateTrait;
use Zend\Http\Headers;
use Zend\Http\Request as HttpRequest;
use Zend\Http\Response as HttpResponse;
use Zend\Mvc\MvcEvent;

/**
 * Since caching dynamic or not intended for caching
 * data could be worse than not caching at all,
 * instructions disabling cache should always win.
 */
class HttpCacheListener implements ListenerAggregateInterface
{
    use ListenerAggregateTrait;

    /**
     * @var array
     */
    protected $config = array();

    /**
     * @var array
     */
    protected $defaultHttpMethodsBlackList = array(
        Httprequest::METHOD_CONNECT,
        Httprequest::METHOD_DELETE,
        Httprequest::METHOD_HEAD,
        Httprequest::METHOD_OPTIONS,
        Httprequest::METHOD_PATCH,
        Httprequest::METHOD_POST,
        Httprequest::METHOD_PROPFIND,
        Httprequest::METHOD_PUT,
        Httprequest::METHOD_TRACE,
    );

    /**
     * @var array
     */
    protected $defaultHttpMethodsWhiteList = array(
        Httprequest::METHOD_GET,
    );

    /**
     * @param EventManagerInterface $events
     * @param int                   $priority
     */
    public function attach(EventManagerInterface $events, $priority = 1)
    {
        $request = $e->getRequest();

        if (
           ! empty($this->config['enabled'])
           and $request instanceof HttpRequest
        ) {
            $this->listeners[] = $events->attach(MvcEvent::EVENT_ROUTE, array($this, 'onRoute'), -1000);
            $this->listeners[] = $events->attach(MvcEvent::EVENT_FINISH, array($this, 'onFinish'), -1000);
        }
    }

    /**
     * @param  MvcEvent $e
     * @return null|
     */
    public function onRoute(MvcEvent $e)
    {
        /* @var $request HttpRequest */
        $request = $e->getRequest();
        if (! $request instanceof HttpRequest) {
            return;
        }

        /* @var $response Response */
        $response = $e->getResponse();
        if (! $response instanceof HttpResponse) {
            return;
        }

        // check enabled (could be disabled during dispatch loop)
        if (empty($this->config['enabled'])) {
            return;
        }

        // check HTTP method
        $method = $request->getMethod();

        if (in_array($method, (array) $this->config['http_methods_black_list'])) {
            return;
        }

        if (! in_array($method, (array) $this->config['http_methods_white_list'])) {
            return;
        }

        /* @var $headers Headers */
        $headers = $request->getHeaders();

        if ($headers->has('Etag')) {
        }

        if ($headers->has('If-Modified-Since')) {
            $ifModifiedSince = $request->getHeader('If-Modified-Since');
        }

        if ($headers->has('If-Not-Modified-Since')) {
            $ifNotModifiedSince = $request->getHeader('If-Not-Modified-Since');
        }

        if ($headers->has('Last-Modified')) {
            $lastModified = $request->getHeader('Last-Modified');
        }
    }

    /**
     * @param MvcEvent $e
     */
    public function onFinish(MvcEvent $e)
    {
        // check enabled (could be disabled during dispatch loop)
        if (empty($this->config['enabled'])) {
            return;
        }

        /* @var $response HttpResponse */
        $response = $e->getResponse();

        // check HTTP code white list + black list
        $statusCode = $response->getStatusCode();

        if (
            ! empty($this->config['http_codes_white_list'])
            and ! in_array($statusCode, (array) $this->config['http_codes_white_list'])
        ) {
            return;
        }

        // Only 200 responses are cached by default
        if (empty($this->config['http_codes_black_list'])) {
            if (! $response->isOk()) {
                return;
            }
        } elseif (! in_array($statusCode, (array) $this->config['http_codes_black_list'])) {
            return;
        }

        // Age
        // CacheControl
        // Etag
        // Expires
        // IfModifiedSince
        // IfUnmodifiedSince
        // LastModified
        // Pragma
    }

    /**
     * @param  array $config
     * @return self
     */
    public function setConfig(array $config)
    {
        if (empty($config['http_methods_black_list'])) {
            $config['http_methods_black_list'] = $this->defaultHttpMethodsBlackList;
        }

        if (empty($config['http_methods_white_list'])) {
            $config['http_methods_white_list'] = $this->defaultHttpMethodsWhiteList;
        }

        $this->config = $config;

        return $this;
    }
}
