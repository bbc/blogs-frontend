<?php
declare(strict_types = 1);

namespace App\BlogsService\Service;

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

    public function getAllBlogs($ttl = CacheInterface::NORMAL, $nullTtl = CacheInterface::NORMAL): IsiteResult
    {
        $cacheKey = $this->cache->keyHelper(__CLASS__, __FUNCTION__, $ttl);

        return $this->cache->getOrSet(
            $cacheKey,
            $ttl,
            function () {
                //@TODO Remember to stop calls if this fails too many times within a given period
                $response = $this->repository->getAllBlogs();
                return $this->responseHandler->getIsiteResult($response);
            },
            [],
            $nullTtl
        );
    }

    public function getBlogById(string $blogId, $ttl = CacheInterface::NORMAL, $nullTtl = CacheInterface::NORMAL): IsiteResult
    {
        $cacheKey = $this->cache->keyHelper(__CLASS__, __FUNCTION__, $blogId, $ttl);

        return $this->cache->getOrSet(
            $cacheKey,
            $ttl,
            function () use ($blogId) {
                //@TODO Remember to stop calls if this fails too many times within a given period
                $response = $this->repository->getBlogById($blogId);
                return $this->responseHandler->getIsiteResult($response);
            },
            [],
            $nullTtl
        );
    }
}
