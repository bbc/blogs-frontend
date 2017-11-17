<?php
declare(strict_types=1);

namespace App\BlogsService\Repository;

use App\BlogsService\Infrastructure\IsiteResultException;
use GuzzleHttp\ClientInterface;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\GuzzleException;
use Psr\Http\Message\ResponseInterface;

abstract class AbstractRepository
{
    /** @var string */
    protected $apiEndpoint;

    /** @var ClientInterface */
    protected $client;

    public function __construct(string $apiEndpoint, ClientInterface $client)
    {
        $this->apiEndpoint = $apiEndpoint;
        $this->client = $client;
    }

    protected function getResponse(string $url): ?ResponseInterface
    {
        try {
            return $this->client->request('GET', $url);
        } catch (GuzzleException $e) {
            if ($e instanceof ClientException && $e->getCode() == 404) {
                return null;
            }
            throw new IsiteResultException('There was an error retrieving data from iSite.', 0, $e);
        }
    }
}