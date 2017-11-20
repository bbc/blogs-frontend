<?php
declare(strict_types=1);

namespace App\BlogsService\Repository;

use App\BlogsService\Domain\Blog;
use App\BlogsService\Query\IsiteQuery\SearchQuery;
use Psr\Http\Message\ResponseInterface;

class TagRepository extends AbstractRepository
{
    public function getTagsByBlog(Blog $blog, int $page, int $limit, bool $sortByName): ?ResponseInterface
    {
        $query = new SearchQuery();

        $query->setProject($blog->getId());
        $query->setNamespace($blog->getId(), 'blogs-tag');
        $query->setQuery(["ns:name", "contains", "*", ]);

        if ($sortByName) {
            $query->setSort([
                [
                    'elementPath' => '/ns:form/ns:metadata/ns:name',
                    'direction' => 'asc',
                ],
            ]);
        }

        $query->setPage($page);
        $query->setPageSize($limit);
        $query->setUnfiltered(true);

        return $this->getResponse($this->apiEndpoint . '/search?q=' . urlencode(json_encode($query->getSearchQuery())));
    }
}
