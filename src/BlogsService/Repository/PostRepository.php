<?php
declare(strict_types=1);

namespace App\BlogsService\Repository;

use App\BlogsService\Domain\Blog;
use App\BlogsService\Domain\ValueObject\IsiteDate;
use App\BlogsService\Infrastructure\IsiteResultException;
use App\BlogsService\Query\IsiteQuery\SearchQuery;
use DateTimeImmutable;
use GuzzleHttp\ClientInterface;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\GuzzleException;
use Psr\Http\Message\ResponseInterface;

class PostRepository
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

    public function getPostsByBlog(Blog $blog, DateTimeImmutable $publishedUntil, int $page, int $perpage, string $sort): ?ResponseInterface
    {
        $isiteDate = (string) new IsiteDate($publishedUntil);

        $query = new SearchQuery();

        $query->setProject($blog->getId());
        $query->setNamespace($blog->getId(), 'blogs-post');

        $query->setQuery([
            'and' => [
                [
                    "ns:published-date",
                    "<=",
                    $isiteDate,
                    "dateTime"
                ]
            ]
        ]);

        $query->setSort([
            [
                'elementPath' => '/ns:form/ns:metadata/ns:published-date',
                'direction' => $sort
            ]
        ]);

        $query->setDepth(1);
        $query->setPage($page);
        $query->setPageSize($perpage);
        $query->setUnfiltered(true);

        return $this->getResponse($this->apiEndpoint . '/search?q=' . urlencode(json_encode($query->getSearchQuery())));
    }

    private function getResponse(string $url): ?ResponseInterface
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
