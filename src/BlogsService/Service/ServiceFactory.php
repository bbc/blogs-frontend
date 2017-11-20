<?php
declare(strict_types = 1);
namespace App\BlogsService\Service;

use App\BlogsService\Infrastructure\Cache\Cache;
use App\BlogsService\Infrastructure\IsiteFeedResponseHandler;
use App\BlogsService\Infrastructure\MapperFactory;
use App\BlogsService\Infrastructure\XmlParser;
use App\BlogsService\Repository\BlogRepository;
use App\BlogsService\Repository\PostRepository;
use App\BlogsService\Repository\TagRepository;
use GuzzleHttp\ClientInterface;

class ServiceFactory
{
    /** @var array */
    protected $instances = [];

    /** @var string */
    protected $apiEndpoint;

    /** @var MapperFactory */
    protected $mapperFactory;

    /** @var Cache */
    protected $cache;

    /** @var ClientInterface */
    protected $client;

    /** @var XmlParser */
    protected $xmlParser;

    public function __construct(string $apiEndpoint, ClientInterface $client, MapperFactory $mapperFactory, Cache $cache, XmlParser $xmlParser)
    {
        $this->cache = $cache;
        $this->mapperFactory = $mapperFactory;
        $this->apiEndpoint = $apiEndpoint;
        $this->client = $client;
        $this->xmlParser = $xmlParser;
    }

    public function getBlogService(): BlogService
    {
        if (!isset($this->instances[BlogService::class])) {
            $this->instances[BlogService::class] = new BlogService(
                new BlogRepository($this->apiEndpoint, $this->client),
                new IsiteFeedResponseHandler($this->mapperFactory->createBlogsmetadataMapper(), $this->xmlParser),
                $this->cache
            );
        }

        return $this->instances[BlogService::class];
    }

    public function getPostService(): PostService
    {
        if (!isset($this->instances[PostService::class])) {
            $this->instances[PostService::class] = new PostService(
                new PostRepository($this->apiEndpoint, $this->client),
                new IsiteFeedResponseHandler($this->mapperFactory->createPostMapper(), $this->xmlParser),
                $this->cache
            );
        }

        return $this->instances[PostService::class];
    }

    public function getTagService(): TagService
    {
        if (!isset($this->instances[TagService::class])) {
            $this->instances[TagService::class] = new TagService(
                new TagRepository($this->apiEndpoint, $this->client),
                new IsiteFeedResponseHandler($this->mapperFactory->createTagMapper(), $this->xmlParser),
                $this->cache
            );
        }

        return $this->instances[TagService::class];
    }
}
