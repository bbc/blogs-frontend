<?php
declare(strict_types = 1);

namespace App\BlogsService\Service;

use App\BlogsService\Domain\Blog;
use App\BlogsService\Domain\Post;
use App\BlogsService\Domain\Tag;
use App\BlogsService\Domain\ValueObject\GUID;
use App\BlogsService\Infrastructure\Cache\CacheInterface;
use App\BlogsService\Infrastructure\IsiteFeedResponseHandler;
use App\BlogsService\Infrastructure\IsiteResult;
use App\BlogsService\Repository\PostRepository;
use DateInterval;
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

    public function getPostByGuid(
        GUID $guid,
        ?Blog $blog = null,
        $ttl = CacheInterface::NORMAL,
        $nullTtl = CacheInterface::NONE
    ): ?Post {
        $blogId = $blog ? $blog->getId() : '';
        $guidString =  (string) $guid;
        $cacheKey = $this->cache->keyHelper(__CLASS__, __FUNCTION__, $guidString, $blogId, $ttl, $nullTtl);

        return $this->cache->getOrSet(
            $cacheKey,
            $ttl,
            function () use ($guidString, $blogId) {
                $response = $this->repository->getPostByGuid($guidString, $blogId);
                $result = $this->responseHandler->getIsiteResult($response);

                return $result->getDomainModels()[0] ?? null;
            },
            [],
            $nullTtl
        );
    }

    public function getPostsAfter(
        Blog $blog,
        DateTimeImmutable $publishedDate,
        DateTimeImmutable $publishedUntil,
        int $page = 1,
        int $perpage = 1,
        $ttl = CacheInterface::NORMAL,
        $nullTtl = CacheInterface::NONE
    ): ?Post {
        $cacheKey = $this->cache->keyHelper(__CLASS__, __FUNCTION__, $blog->getId(), $publishedDate->getTimestamp(), $publishedUntil->getTimestamp(), $page, $perpage, $ttl, $nullTtl);

        return $this->cache->getOrSet(
            $cacheKey,
            $ttl,
            function () use ($blog, $publishedDate, $publishedUntil, $page, $perpage) {
                //@TODO Remember to stop calls if this fails too many times within a given period
                $response = $this->repository->getPostsBetween($blog->getId(), $publishedDate, $publishedUntil, 0, $page, $perpage, 'asc');
                $result = $this->responseHandler->getIsiteResult($response);

                return $result->getDomainModels()[0] ?? null;
            },
            [],
            $nullTtl
        );
    }

    public function getPostsBefore(
        Blog $blog,
        DateTimeImmutable $publishedDate,
        int $page = 1,
        int $perpage = 1,
        $ttl = CacheInterface::NORMAL,
        $nullTtl = CacheInterface::NONE
    ): ?Post {
        $cacheKey = $this->cache->keyHelper(__CLASS__, __FUNCTION__, $blog->getId(), $publishedDate->getTimestamp(), $page, $perpage, $ttl, $nullTtl);

        return $this->cache->getOrSet(
            $cacheKey,
            $ttl,
            function () use ($blog, $publishedDate, $page, $perpage) {
                //@TODO Remember to stop calls if this fails too many times within a given period
                $response = $this->repository->getPostsBetween($blog->getId(), new DateTimeImmutable('1970-01-01'), $publishedDate->sub(new DateInterval('PT1S')), 0, $page, $perpage, 'desc');
                $result = $this->responseHandler->getIsiteResult($response);

                return $result->getDomainModels()[0] ?? null;
            },
            [],
            $nullTtl
        );
    }

    public function getPostsByBlog(
        Blog $blog,
        DateTimeImmutable $publishedUntil,
        int $page = 1,
        int $perpage = 10,
        string $sort = 'desc',
        $ttl = CacheInterface::NORMAL,
        $nullTtl = CacheInterface::NONE
    ): IsiteResult {
        $cacheKey = $this->cache->keyHelper(__CLASS__, __FUNCTION__, $blog->getId(), $page, $perpage, $sort, $ttl, $nullTtl);

        return $this->cache->getOrSet(
            $cacheKey,
            $ttl,
            function () use ($blog, $publishedUntil, $page, $perpage, $sort) {
                //@TODO Remember to stop calls if this fails too many times within a given period
                $response = $this->repository->getPostsBetween($blog->getId(), new DateTimeImmutable('1970-01-01'), $publishedUntil, 1, $page, $perpage, $sort);
                return $this->responseHandler->getIsiteResult($response);
            },
            [],
            $nullTtl
        );
    }

    public function getPostsByTag(
        Blog $blog,
        Tag $tag,
        int $page = 1,
        int $perpage = 10,
        $ttl = CacheInterface::NORMAL,
        $nullTtl = CacheInterface::NONE
    ): IsiteResult {
        $cacheKey = $this->cache->keyHelper(__CLASS__, __FUNCTION__, $blog->getId(), $tag->getId(), $page, $perpage, $ttl, $nullTtl);

        return $this->cache->getOrSet(
            $cacheKey,
            $ttl,
            function () use ($blog, $tag, $page, $perpage) {
                //@TODO Remember to stop calls if this fails too many times within a given period
                $response = $this->repository->getPostsByTagFileId($blog->getId(), (string) $tag->getFileId(), $page, $perpage);
                return $this->responseHandler->getIsiteResult($response);
            },
            [],
            $nullTtl
        );
    }
}
