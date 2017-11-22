<?php
declare(strict_types=1);

namespace App\BlogsService\Repository;

use App\BlogsService\Query\IsiteQuery\GuidQuery;
use App\BlogsService\Query\IsiteQuery\SearchQuery;
use DateInterval;
use DateTimeImmutable;
use Psr\Http\Message\ResponseInterface;

class PostRepository extends AbstractRepository
{
    public function getPostByGuid(string $guid, string $blogId = ''): ?ResponseInterface
    {
        $query = new GuidQuery();

        $query->setContentId($guid);
        if ($blogId) {
            $query->setProject($blogId);
        }

        return $this->getResponse($query);
    }

    public function getPostsAfter(
        string $blogId,
        DateTimeImmutable $publishedDate,
        DateTimeImmutable $publishedUntil,
        int $page,
        int $perpage
    ): ?ResponseInterface {
        $query = new SearchQuery();
        $query->setProject($blogId);
        $query->setNamespace($blogId, 'blogs-post');

        $query->setQuery([
            'and' => [
                [
                    'ns:published-date',
                    '>',
                    $publishedDate->add(new DateInterval('PT1S'))->format('Y-m-d\TH:i:s.BP'),
                    'dateTime',
                ],
                [
                    'ns:published-date',
                    '<=',
                    $publishedUntil->format('Y-m-d\TH:i:s.BP'),
                    'dateTime',
                ],
            ],
        ]);

        $query->setSort([
            [
                'elementPath' => '/ns:form/ns:metadata/ns:published-date',
                'direction' => 'asc',
            ],
        ]);
        $query->setDepth(0);
        $query->setPage($page);
        $query->setPageSize($perpage);
        $query->setUnfiltered(true);

        return $this->getResponse($query);
    }

    public function getPostsByBlogPublishedBefore(string $blogId, DateTimeImmutable $publishedUntil, int $depth, int $page, int $perpage, string $sort): ?ResponseInterface
    {
        $query = new SearchQuery();

        $query->setProject($blogId);
        $query->setNamespace($blogId, 'blogs-post');

        $query->setQuery([
            'and' => [
                [
                    'ns:published-date',
                    '<=',
                    $publishedUntil->format('Y-m-d\TH:i:s.BP'),
                    'dateTime',
                ],
            ],
        ]);

        $query->setSort([
            [
                'elementPath' => '/ns:form/ns:metadata/ns:published-date',
                'direction' => $sort,
            ],
        ]);

        $query->setDepth($depth);
        $query->setPage($page);
        $query->setPageSize($perpage);
        $query->setUnfiltered(true);

        return $this->getResponse($query);
    }
}
