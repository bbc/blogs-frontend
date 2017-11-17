<?php
declare(strict_types = 1);
namespace App\BlogsService\Repository;

use App\BlogsService\Infrastructure\IsiteResultException;
use App\BlogsService\Query\IsiteQuery\FileIdQuery;
use App\BlogsService\Query\IsiteQuery\SearchQuery;
use GuzzleHttp\ClientInterface;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\GuzzleException;
use Psr\Http\Message\ResponseInterface;

class BlogRepository
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

    public function getAllBlogs(): ?ResponseInterface
    {
        $query = new SearchQuery();
        $query->setSearchChildrenOfProject('blogs')
            ->setFileType('blogsmetadata')
            ->setQuery(["or" => [['blog-name', 'contains', '*']]])
            ->setSort([["elementPath" => "/*:form/*:metadata/*:blog-name"]])
            ->setDepth(0)
            ->setUnfiltered(true);

        return $this->getResponse($this->apiEndpoint . '/search?q=' . json_encode($query->getSearchQuery()));
    }

    public function getBlogById(string $blogId): ?ResponseInterface
    {
        $query = new FileIdQuery();
        $query->setProject('blogs-' . $blogId)
            ->setId('blogs-meta-data')
            ->setDepth(2);

        return $this->getResponse($this->apiEndpoint . '/content/file?' . http_build_query($query->getParameters()));
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
