<?php

/**
 * @see       https://github.com/laminas-api-tools/api-tools-http-cache for the canonical source repository
 * @copyright https://github.com/laminas-api-tools/api-tools-http-cache/blob/master/COPYRIGHT.md
 * @license   https://github.com/laminas-api-tools/api-tools-http-cache/blob/master/LICENSE.md New BSD License
 */

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
