<?php
declare(strict_types = 1);

namespace App\BlogsService\Service;

use App\BlogsService\Domain\Author;
use App\BlogsService\Domain\Blog;
use App\BlogsService\Domain\Post;
use App\BlogsService\Domain\Tag;
use App\BlogsService\Domain\ValueObject\GUID;
use App\BlogsService\Infrastructure\Cache\CacheInterface;
use App\BlogsService\Infrastructure\IsiteFeedResponseHandler;
use App\BlogsService\Infrastructure\IsiteResult;
use App\BlogsService\Repository\PostRepository;
use Cake\Chronos\Date;
use DateInterval;
use DateTimeImmutable;
use Cake\Chronos\Chronos;

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

    public function getPostsByAuthor(
        Blog $blog,
        Author $author,
        int $page = 1,
        int $perPage = 20,
        $ttl = CacheInterface::NORMAL,
        $nullTtl = CacheInterface::NONE
    ): IsiteResult {
        $cacheKey = $this->cache->keyHelper(__CLASS__, __FUNCTION__, $blog->getId(), $author->getFileId(), $page, $perPage, $ttl, $nullTtl);

        return $this->cache->getOrSet(
            $cacheKey,
            $ttl,
            function () use ($blog, $author, $page, $perPage) {
                //@TODO Remember to stop calls if this fails too many times within a given period
                $response = $this->repository->getPostsByAuthorFileId($blog->getId(), (string) $author->getFileId(), $page, $perPage);
                return $this->responseHandler->getIsiteResult($response);
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

    public function getPostsByMonth(
        Blog $blog,
        int $year,
        int $month,
        int $page = 1,
        int $perpage = 10,
        string $sort = 'desc',
        $ttl = CacheInterface::NORMAL,
        $nullTtl = CacheInterface::NONE
    ): IsiteResult {

        $dateFrom = Chronos::create($year, $month, 2)->startOfMonth();
        $dateUntil = Chronos::create($year, $month, 2)->endOfMonth();

        $cacheKey = $this->cache->keyHelper(__CLASS__, __FUNCTION__, $blog->getId(), $dateFrom, $dateUntil, $page, $perpage, $sort, $ttl, $nullTtl);

        return $this->cache->getOrSet(
            $cacheKey,
            $ttl,
            function () use ($blog, $dateFrom, $dateUntil, $page, $perpage, $sort) {
                //@TODO Remember to stop calls if this fails too many times within a given period
                $response = $this->repository->getPostsBetween($blog->getId(), $dateFrom, $dateUntil, 1, $page, $perpage, $sort);
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

    /** @return int[] */
    public function getPostCountForMonthsInYear(
        Blog $blog,
        int $year,
        array $months,
        $ttl = CacheInterface::X_LONG,
        $nullTtl = CacheInterface::NONE
    ): array {
        $cacheKey = $this->cache->keyHelper(__CLASS__, __FUNCTION__, $blog->getId(), $year, join('_', $months));

        return $this->cache->getOrSet(
            $cacheKey,
            $ttl,
            function () use ($blog, $year, $months) {
                $responses = $this->repository->getPostsForMonthsInYear($blog->getId(), $year, $months, 1, 1, 1, 'desc');

                $result = [];

                foreach ($responses as $key => $response) {
                    $result[$key] = $this->responseHandler->getIsiteResult($response)->getTotal();
                }

                return $result;
            },
            [],
            $nullTtl
        );
    }
}
