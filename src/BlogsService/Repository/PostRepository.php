<?php
declare(strict_types = 1);

namespace App\BlogsService\Repository;

use App\BlogsService\Query\IsiteQuery\GuidQuery;
use App\BlogsService\Query\IsiteQuery\SearchQuery;
use DateInterval;
use DateTimeImmutable;
use Psr\Http\Message\ResponseInterface;

class PostRepository extends AbstractRepository
{
    public function getPostsByAuthorFileId(string $blogId, string $authorFileId, int $page, int $perpage)
    {
        $query = new SearchQuery();
        $query->setProject($blogId);
        $query->setNamespace($blogId, 'blogs-post');
        $query->setQuery([
            'ns:author',
            'contains',
            $authorFileId,
        ]);
        $query->setDepth(1);
        $query->setPage($page);
        $query->setPageSize($perpage);
        $query->setUnfiltered(true); //Experimental
        $query->setSort([
            [
                'elementPath' => '/ns:form/ns:metadata/ns:published-date',
                'direction' => 'desc',
            ],
        ]);

        return $this->getResponse($query);
    }

    public function getPostByGuid(string $guid, string $blogId = ''): ?ResponseInterface
    {
        $query = new GuidQuery();

        $query->setContentId($guid);
        $query->setDepth(1);
        if ($blogId) {
            $query->setProject($blogId);
        }

        return $this->getResponse($query);
    }

    public function getPostsBetween(
        string $blogId,
        DateTimeImmutable $afterDate,
        DateTimeImmutable $beforeDate,
        int $depth,
        int $page,
        int $perpage,
        string $sort
    ): ?ResponseInterface {
        $query = new SearchQuery();
        $query->setProject($blogId);
        $query->setNamespace($blogId, 'blogs-post');

        $query->setQuery([
            'and' => [
                [
                    'ns:published-date',
                    '>',
                    $afterDate->add(new DateInterval('PT1S'))->format('Y-m-d\TH:i:s.BP'),
                    'dateTime',
                ],
                [
                    'ns:published-date',
                    '<=',
                    $beforeDate->format('Y-m-d\TH:i:s.BP'),
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

    public function getPostsByTagFileId(string $blogId, string $tagFileId, int $page, int $perpage): ?ResponseInterface
    {
        $query = new SearchQuery();
        $query->setProject($blogId);
        $query->setNamespace($blogId, 'blogs-post');
        $query->setQuery(['ns:tag', 'contains', $tagFileId]);
        $query->setSort([
            [
                'elementPath' => '/ns:form/ns:metadata/ns:published-date',
                'direction' => 'desc',
            ],
        ]);
        $query->setDepth(1);
        $query->setPage($page);
        $query->setPageSize($perpage);
        $query->setUnfiltered(true);

        return $this->getResponse($query);
    }
}
