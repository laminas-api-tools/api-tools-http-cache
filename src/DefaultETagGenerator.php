<?php

namespace Laminas\ApiTools\HttpCache;

use Laminas\Http\Request as HttpRequest;
use Laminas\Http\Response as HttpResponse;

class DefaultETagGenerator implements ETagGeneratorInterface
{
    /**
     * {@inheritDoc}
     */
    public function generate(HttpRequest $request, HttpResponse $response)
    {
        return md5($response->getContent());
    }
}
