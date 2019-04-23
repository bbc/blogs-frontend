<?php
declare (strict_types = 1);

namespace App\BlogsService\Service;

use Symfony\Component\HttpFoundation\Response;
use GuzzleHttp\ClientInterface;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\GuzzleException;

class LegacyBlogService
{
    /** @var ClientInterface */
    protected $guzzleClient;

    public function __construct(ClientInterface $guzzle)
    {
        $this->guzzleClient = $guzzle;
    }

    public function getLegacyBlog(string $path): ?Response
    {
        $path = substr($path, 0, 1000);
        $url = 'https://archivewww.live.bbc.co.uk/' . $path;
        try {
            $httpResponse = $this->guzzleClient->request('GET', $url, [
                'allow_redirects' => false,
            ]);
            if ($httpResponse->getStatusCode() !== Response::HTTP_OK) {
                return null;
            }
        } catch (ClientException $e) {
            // 4xx level error
            return null;
        } catch (GuzzleException $e) {
            // This exception covers things like networking issues or 500 errors.
            throw $e;
        }

        $body = $httpResponse->getBody()->getContents();
        $contentType = '';
        if ($contentTypesArray = $httpResponse->getHeader('content-type')) {
            $contentType = reset($contentTypesArray);
        }

        if (stristr($contentType, 'text/html')) {
            // Basically legacy blogs are http pages with lots of hardcoded http:// links.
            // We rewrite these to https with an almost complete disregard for html semantics and indeed common sense
            // here because making this site selectively accessible over http for this edge case is more trouble
            // than it is worth.
            $body = str_ireplace('http://', 'https://', $body);
        }

        $response = new Response($body, Response::HTTP_OK, [
            'content-type' => $httpResponse->getHeader('content-type'),
        ]);
        $response->setPublic()->setMaxAge(300);

        return $response;
    }
}
