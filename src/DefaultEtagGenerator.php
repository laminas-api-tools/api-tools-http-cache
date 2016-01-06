<?php
/**
 * Created by PhpStorm.
 * User: adri
 * Date: 06/01/16
 * Time: 16:04
 */

namespace ZF\HttpCache;

use Zend\Http\Response as HttpResponse;

class DefaultEtagGenerator implements EtagGeneratorInterface
{
    /**
     * Returns an ETag for the given response.
     *
     * @param HttpResponse $response
     * @return string Etag
     */
    public function generateEtag(HttpResponse $response)
    {
        return md5($response->getContent());
    }
}
