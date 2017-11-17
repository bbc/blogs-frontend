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

    /** @var BlogRepository */
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

    public function getPostsByBlog(Blog $blog, DateTimeImmutable $publishedUntil, ?int $page, ?int $perpage, ?string $sort): IsiteResult
    {

    }
}
