<?php
declare(strict_types = 1);

namespace App\BlogsService\Service;

use App\BlogsService\Domain\Blog;
use App\BlogsService\Infrastructure\Cache\CacheInterface;
use App\BlogsService\Infrastructure\IsiteFeedResponseHandler;
use App\BlogsService\Infrastructure\IsiteResult;
use App\BlogsService\Repository\BlogRepository;
use App\BlogsService\Repository\PostRepository;
use DateTimeImmutable;

class PostService
{
    /** @var  IsiteFeedResponseHandler */
    protected $responseHandler;

    /** @var PostRepository */
    protected $repository;

    /** @var CacheInterface */
    protected $cache;

    public function __construct(
        PostRepository $repository,
        IsiteFeedResponseHandler $responseHandler,
        CacheInterface $cache
    ) {
        $this->repository = $repository;
        $this->responseHandler = $responseHandler;
        $this->cache = $cache;
    }

    public function getPostsByBlog(
        Blog $blog,
        DateTimeImmutable $publishedUntil,
        int $page = 1,
        int $perpage = 10,
        string $sort = 'desc',
        $ttl = CacheInterface::NORMAL,
        $nullTtl = CacheInterface::NORMAL
    ): IsiteResult {
        $cacheKey = $this->cache->keyHelper(__CLASS__, __FUNCTION__, $ttl, $blog->getId());

        return $this->cache->getOrSet(
            $cacheKey,
            $ttl,
            function () use ($blog, $publishedUntil, $page, $perpage, $sort) {
                //@TODO Remember to stop calls if this fails too many times within a given period
                $response = $this->repository->getPostsByBlog($blog, $publishedUntil, $page, $perpage, $sort);
                return $this->responseHandler->getIsiteResult($response);
            },
            [],
            $nullTtl
        );
    }
}
