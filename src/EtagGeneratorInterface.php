<?php

namespace ZF\HttpCache;

use Zend\Http\Request as HttpRequest;
use Zend\Http\Response as HttpResponse;

interface EtagGeneratorInterface
{
    /**
     * Returns an ETag for the given response.
     *
     * @param HttpRequest $request
     * @param HttpResponse $response
     * @return string Etag
     */
    public function generateEtag(HttpRequest $request, HttpResponse $response);
}
