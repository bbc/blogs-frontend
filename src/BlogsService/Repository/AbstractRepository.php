<?php
declare(strict_types=1);

namespace App\BlogsService\Repository;

use App\BlogsService\Query\IsiteQuery\QueryInterface;
use App\BlogsService\Infrastructure\IsiteResultException;
use GuzzleHttp\ClientInterface;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\GuzzleException;
use Psr\Http\Message\ResponseInterface;
use RuntimeException;

abstract class AbstractRepository
{
    /** @var string */
    private $apiEndpoint;

    /** @var ClientInterface */
    private $client;

    public function __construct(string $apiEndpoint, ClientInterface $client)
    {
        $this->apiEndpoint = $apiEndpoint;
        $this->client = $client;
    }

    protected function getResponse(QueryInterface $query): ?ResponseInterface
    {
        try {
            return $this->client->request('GET', $this->apiEndpoint . $query->getPath());
        } catch (GuzzleException $e) {
            if ($e instanceof ClientException && $e->getCode() == 404) {
                return null;
            }
            throw new IsiteResultException('There was an error retrieving data from iSite.', 0, $e);
        }
    }

    /**
     * @param QueryInterface[] $queries
     * @return ResponseInterface[] array
     */
    protected function getParallelResponses(array $queries): array
    {
        $promises = [];
        foreach ($queries as $key => $query) {
            if (!$query instanceof QueryInterface) {
                throw new RuntimeException('Encountered element in `$queries` array that was not a QueryInterface');
            }
            $promises[$key] = $this->client->requestAsync('GET', $this->apiEndpoint . $query->getPath());
        }

        $results = [];
        foreach ($promises as $key => $promise) {
            try {
                $results[$key] = $promise->wait();
            } catch (GuzzleException $e) {
                if ($e instanceof ClientException && $e->getCode() == 404) {
                    $results[$key] = null;
                }
                throw new IsiteResultException('There was an error retrieving data from iSite.', 0, $e);
            }
        }

        return $results;
    }
}
