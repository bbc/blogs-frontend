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
        $cacheKey = uniqid().$this->cache->keyHelper(__CLASS__, __FUNCTION__, $guidString, $blogId, $ttl, $nullTtl);

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
        $cacheKey = uniqid().$this->cache->keyHelper(__CLASS__, __FUNCTION__, $blog->getId(), $publishedDate->getTimestamp(), $page, $perpage, $ttl, $nullTtl);

        return $this->cache->getOrSet(
            $cacheKey,
            $ttl,
            function () use ($blog, $publishedDate, $page, $perpage) {
                $publishedDate = $publishedDate->setTimezone("UTC");
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
dump($publishedDate, $publishedDate->format('Y-m-d\TH:i:s.BP'),$blog, $ranges, $result);die();
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
                $response = $this->repository->getPostsByTagFileIds($blog->getId(), [(string) $tag->getFileId()], $page, $perpage);
                return $this->responseHandler->getIsiteResult($response[0]);
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
        $tagFileIds = [];
        foreach ($tags as $tag) {
            $tagFileIds[$tag->getId()] = (string) $tag->getFileId();
        }

        $cacheKey = $this->cache->keyHelper(__CLASS__, __FUNCTION__, $blog->getId(), join('_', $tagFileIds));

        return $this->cache->getOrSet(
            $cacheKey,
            $ttl,
            function () use ($blog, $tagFileIds) {
                $responses = $this->repository->getPostsByTagFileIds($blog->getId(), $tagFileIds, 1, 1);

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
//
//https://api.test.bbc.co.uk/isite2-content-reader/search?q=
////%7B%22project%22%3A%22blogs-demo%22%2C%22namespaces%22%3A%7B%22ns%22%3A%22https%3A%5C%2F%5C%2Fproduction.bbc.co.uk%5C%2Fisite2%5C%2Fproject%5C%2Fblogs-demo%5C%2Fblogs-post%22%7D%2C%22query%22%3A%7B%22and%22%3A%5B%5B%22ns%3Apublished-date%22%2C%22%3E%22%2C%221970-01-01T11%3A47%3A42.533%2B00%3A00%22%2C%22dateTime%22%5D%2C%5B%22ns%3Apublished-date%22%2C%22%3C%3D%22%2C%222018-09-12T13%3A48%3A59.000%2B00%3A00%22%2C%22dateTime%22%5D%5D%7D%2C%22sort%22%3A%5B%7B%22elementPath%22%3A%22%5C%2Fns%3Aform%5C%2Fns%3Ametadata%5C%2Fns%3Apublished-date%22%2C%22direction%22%3A%22desc%22%7D%5D%2C%22depth%22%3A0%2C%22page%22%3A%221%22%2C%22pageSize%22%3A%221%22%2C%22unfiltered%22%3Atrue%7D
