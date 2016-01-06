<?php

namespace ZF\HttpCache;

use Zend\Http\Response as HttpResponse;

interface EtagGeneratorInterface
{
    /**
     * Returns an ETag for the given response.
     *
     * @param HttpResponse $routeMatch
     * @return string Etag
     */
    public function generateEtag(HttpResponse $response);
}
