<?php
declare(strict_types = 1);

namespace App\BlogsService\Service;

use App\BlogsService\Domain\Blog;
use App\BlogsService\Infrastructure\Cache\CacheInterface;
use App\BlogsService\Infrastructure\IsiteFeedResponseHandler;
use App\BlogsService\Infrastructure\IsiteResult;
use App\BlogsService\Repository\TagRepository;

class TagService
{
    /** @var  IsiteFeedResponseHandler */
    protected $responseHandler;

    /** @var TagRepository */
    protected $repository;

    /** @var CacheInterface */
    protected $cache;

    public function __construct(
        TagRepository $repository,
        IsiteFeedResponseHandler $responseHandler,
        CacheInterface $cache
    ) {
        $this->repository = $repository;
        $this->responseHandler = $responseHandler;
        $this->cache = $cache;
    }

    public function getTagsByBlog(
        Blog $blog,
        int $page = 1,
        int $limit = 20,
        bool $sortByName = true,
        $ttl = CacheInterface::NORMAL,
        $nullTtl = CacheInterface::NONE
    ): IsiteResult {
        $cacheKey = $this->cache->keyHelper(__CLASS__, __FUNCTION__, $blog->getId(), $page, $limit, $sortByName, $ttl, $nullTtl);

        return $this->cache->getOrSet(
            $cacheKey,
            $ttl,
            function () use ($blog, $page, $limit, $sortByName) {
                $response = $this->repository->getTagsByBlog($blog->getId(), $page, $limit, $sortByName);
                return $this->responseHandler->getIsiteResult($response);
            },
            [],
            $nullTtl
        );
    }
}
