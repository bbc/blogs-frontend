<?php
declare(strict_types = 1);

namespace App\BlogsService\Service;

use App\BlogsService\Domain\Author;
use App\BlogsService\Domain\Blog;
use App\BlogsService\Domain\Post;
use App\BlogsService\Domain\Tag;
use App\BlogsService\Domain\ValueObject\GUID;
use App\BlogsService\Infrastructure\IsiteFeedResponseHandler;
use App\BlogsService\Infrastructure\IsiteResult;
use App\BlogsService\Repository\PostRepository;
use BBC\ProgrammesCachingLibrary\CacheInterface;
use Cake\Chronos\Chronos;

class PostService
{
    /** @var IsiteFeedResponseHandler */
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

    public function getPreviousAndNextPosts(
        Blog $blog,
        Chronos $publishedDate,
        int $page = 1,
        int $perpage = 1,
        $ttl = CacheInterface::NORMAL,
        $nullTtl = CacheInterface::NONE
    ): array {
        $cacheKey = $this->cache->keyHelper(__CLASS__, __FUNCTION__, $blog->getId(), $publishedDate->getTimestamp(), $page, $perpage, $ttl, $nullTtl);

        return $this->cache->getOrSet(
            $cacheKey,
            $ttl,
            function () use ($blog, $publishedDate, $page, $perpage) {

                $ranges = [
                    'previousPost' => ['afterDate' => Chronos::create(1970, 1, 1), 'beforeDate' => $publishedDate->subSecond(), 'sort' => 'desc'],
                    'nextPost' => ['afterDate' => $publishedDate, 'beforeDate' => Chronos::now(), 'sort' => 'asc'],
                ];

                $responses = $this->repository->getPostsBetween($blog->getId(), $ranges, 0, $page, $perpage);
                $result = [];
                foreach ($responses as $key => $response) {
                    $post = $this->responseHandler->getIsiteResult($response);
                    $result[$key] = $post->getDomainModels()[0] ?? null;
                }

                return [$result['previousPost'], $result['nextPost']];
            },
            [],
            $nullTtl
        );
    }

    public function getOldestPostAndLatestPost(
        Blog $blog,
        Chronos $nowDate,
        int $page = 1,
        int $perpage = 1,
        $ttl = CacheInterface::NORMAL,
        $nullTtl = CacheInterface::NONE
    ): array {
        $cacheKey = $this->cache->keyHelper(__CLASS__, __FUNCTION__, $blog->getId(), $nowDate->getTimestamp(), $page, $perpage, $ttl, $nullTtl);

        return $this->cache->getOrSet(
            $cacheKey,
            $ttl,
            function () use ($blog, $nowDate, $page, $perpage) {
                $beginningOfTime = Chronos::create(1970, 1, 1);

                $ranges = [
                    'oldestPost' => ['afterDate' => $beginningOfTime, 'beforeDate' => $nowDate, 'sort' => 'asc'],
                    'latestPost' => ['afterDate' => $beginningOfTime, 'beforeDate' => $nowDate, 'sort' => 'desc'],
                ];

                $responses = $this->repository->getPostsBetween($blog->getId(), $ranges, 0, $page, $perpage);
                $result = [];
                foreach ($responses as $key => $response) {
                    $post = $this->responseHandler->getIsiteResult($response);
                    $result[$key] = $post->getDomainModels()[0] ?? null;
                }

                return [$result['oldestPost'], $result['latestPost']];
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
                $response = $this->repository->getPostsByAuthorFileId($blog->getId(), (string) $author->getFileId(), $page, $perPage);
                return $this->responseHandler->getIsiteResult($response);
            },
            [],
            $nullTtl
        );
    }

    public function getPostsByBlog(
        Blog $blog,
        Chronos $publishedUntil,
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
                $response = $this->repository->getPostsBetween($blog->getId(), [['afterDate' => Chronos::create(1970, 1, 1), 'beforeDate' => $publishedUntil, 'sort' => $sort]], 1, $page, $perpage);
                return $this->responseHandler->getIsiteResult($response[0]);
            },
            [],
            $nullTtl
        );
    }

    /**
     * @param Blog $blog
     * @param Author[] $authors
     * @param int $page
     * @param int $perPage
     * @param string $ttl
     * @param string $nullTtl
     * @return IsiteResult[]
     */
    public function getPostsForAuthors(
        Blog $blog,
        array $authors,
        int $page = 1,
        int $perPage = 20,
        $ttl = CacheInterface::NORMAL,
        $nullTtl = CacheInterface::NONE
    ): array {
        $authorIds = array_map(
            function (Author $author) {
                return (string) $author->getFileId();
            },
            $authors
        );
        $cacheKey = $this->cache->keyHelper(__CLASS__, __FUNCTION__, $blog->getId(), implode('-', $authorIds), $page, $perPage, $ttl, $nullTtl);

        return $this->cache->getOrSet(
            $cacheKey,
            $ttl,
            function () use ($blog, $authorIds, $page, $perPage) {
                $responses = $this->repository->getPostsForAuthors($blog->getId(), $authorIds, $page, $perPage);

                $result = [];

                foreach ($responses as $key => $response) {
                    $result[$key] = $this->responseHandler->getIsiteResult($response);
                }

                return $result;
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
                $response = $this->repository->getPostsBetween($blog->getId(), [['afterDate' => $dateFrom, 'beforeDate' => $dateUntil, 'sort' => $sort]], 1, $page, $perpage);
                return $this->responseHandler->getIsiteResult($response[0]);
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
                $response = $this->repository->getPostsByTagFileId($blog->getId(), (string) $tag->getFileId(), $page, $perpage);
                return $this->responseHandler->getIsiteResult($response);
            },
            [],
            $nullTtl
        );
    }

    /** @return int[] */
    public function getPostCountsForTags(
        Blog $blog,
        array $tags,
        $ttl = CacheInterface::X_LONG,
        $nullTtl = CacheInterface::NONE
    ): array {
        $tagIds = array_map(
            function (Tag $tag) {
                return (string) $tag->getFileId();
            },
            $tags
        );

        $cacheKey = $this->cache->keyHelper(__CLASS__, __FUNCTION__, $blog->getId(), join('_', $tagIds));

        return $this->cache->getOrSet(
            $cacheKey,
            $ttl,
            function () use ($blog, $tagIds) {
                $responses = $this->repository->getPostsByTagFileIds($blog->getId(), $tagIds, 1, 1);

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
