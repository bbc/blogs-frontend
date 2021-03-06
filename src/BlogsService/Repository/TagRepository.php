<?php
declare(strict_types=1);

namespace App\BlogsService\Repository;

use App\BlogsService\Query\IsiteQuery\SearchQuery;
use Psr\Http\Message\ResponseInterface;

class TagRepository extends AbstractRepository
{
    public function getTagByFileId(string $fileId, string $blogId): ?ResponseInterface
    {
        $query = new SearchQuery();

        $query->setProject($blogId);
        $query->setNamespace($blogId, 'blogs-tag');
        $query->setQuery(['ns:file_id', '=', $fileId]);
        $query->setPage(1);
        $query->setPageSize(1);
        $query->setUnfiltered(true);

        return $this->getResponse($query);
    }

    public function getTagsByBlog(string $blogId, int $page, int $limit, bool $sortByName): ?ResponseInterface
    {
        $query = new SearchQuery();

        $query->setProject($blogId);
        $query->setNamespace($blogId, 'blogs-tag');
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

        return $this->getResponse($query);
    }
}
