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

        $response = new Response($httpResponse->getBody()->getContents(), Response::HTTP_OK, [
            'content-type' => $httpResponse->getHeader('content-type'),
        ]);
        $response->setPublic()->setMaxAge(300);

        return $response;
    }
}
