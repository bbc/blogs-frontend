<?php
declare(strict_types=1);

namespace App\BlogsService\Repository;

use App\BlogsService\Domain\Blog;
use App\BlogsService\Query\IsiteQuery\SearchQuery;
use DateTimeImmutable;
use Psr\Http\Message\ResponseInterface;

class PostRepository extends AbstractRepository
{
    public function getPostsByBlog(Blog $blog, DateTimeImmutable $publishedUntil, int $page, int $perpage, string $sort): ?ResponseInterface
    {
        $query = new SearchQuery();

        $query->setProject($blog->getId());
        $query->setNamespace($blog->getProjectId(), 'blogs-post');

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

        $query->setDepth(1);
        $query->setPage($page);
        $query->setPageSize($perpage);
        $query->setUnfiltered(true);

        return $this->getResponse($this->apiEndpoint . '/search?q=' . urlencode(json_encode($query->getSearchQuery())));
    }
}
