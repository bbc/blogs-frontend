<?php
declare(strict_types = 1);

namespace App\BlogsService\Service;

use App\BlogsService\Domain\Blog;
use App\BlogsService\Infrastructure\Cache\CacheInterface;
use App\BlogsService\Infrastructure\IsiteFeedResponseHandler;
use App\BlogsService\Infrastructure\IsiteResult;
use App\BlogsService\Repository\BlogRepository;

class BlogService
{
    /** @var  IsiteFeedResponseHandler */
    protected $responseHandler;

    /** @var BlogRepository */
    protected $repository;

    /** @var CacheInterface */
    protected $cache;

    public function __construct(
        BlogRepository $repository,
        IsiteFeedResponseHandler $responseHandler,
        CacheInterface $cache
    ) {
        $this->repository = $repository;
        $this->responseHandler = $responseHandler;
        $this->cache = $cache;
    }

    public function getAllBlogs($ttl = CacheInterface::NORMAL, $nullTtl = CacheInterface::NONE): IsiteResult
    {
        $cacheKey = $this->cache->keyHelper(__CLASS__, __FUNCTION__, $ttl, $nullTtl);

        return $this->cache->getOrSet(
            $cacheKey,
            $ttl,
            function () {
                $response = $this->repository->getAllBlogs();
                return $this->responseHandler->getIsiteResult($response);
            },
            [],
            $nullTtl
        );
    }

    public function getBlogById(string $blogId, $ttl = CacheInterface::NORMAL, $nullTtl = CacheInterface::NONE): ?Blog
    {
        $cacheKey = $this->cache->keyHelper(__CLASS__, __FUNCTION__, $blogId, $ttl, $nullTtl);

        return $this->cache->getOrSet(
            $cacheKey,
            $ttl,
            function () use ($blogId) {
                $response = $this->repository->getBlogById($blogId);
                $result = $this->responseHandler->getIsiteResult($response);
                return $result->getDomainModels()[0] ?? null;
            },
            [],
            $nullTtl
        );
    }
}
