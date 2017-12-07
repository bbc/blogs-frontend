<?php
declare(strict_types = 1);

namespace App\BlogsService\Repository;

use App\BlogsService\Query\IsiteQuery\GuidQuery;
use App\BlogsService\Query\IsiteQuery\SearchQuery;
use Psr\Http\Message\ResponseInterface;

class AuthorRepository extends AbstractRepository
{
    public function getAuthorByGUID(string $blogId, string $guid, $preview = false): ?ResponseInterface
    {
        $query = new GuidQuery();
        $query->setProject($blogId);
        $query->setContentId($guid);
        $query->setPreview($preview);

        return $this->getResponse($query);
    }

    public function getAuthorsByLetter(string $blogId, string $letter, int $page, int $limit): ?ResponseInterface
    {
        $query = new SearchQuery();
        $query->setProject($blogId);
        $query->setNamespace($blogId, 'authors');
        $query->setQuery([
            'ns:last-name',
            'contains',
            $letter . '*',
        ]);
        $query->setSort([
            [
                'elementPath' => '/ns:form/ns:metadata/ns:last-name',
                'direction' => 'asc',
            ],
        ]);
        $query->setPage($page);
        $query->setPageSize($limit);

        return $this->getResponse($query);
    }

    public function getAuthorsByBlog(string $blogId, int $page, int $limit): ?ResponseInterface
    {
        $query = new SearchQuery();
        $query->setProject($blogId);
        $query->setNamespace($blogId, 'authors');
        $query->setQuery([
            'ns:last-name',
            'contains',
            '*',
        ]);
        $query->setSort([
            [
                'elementPath' => '/ns:form/ns:metadata/ns:last-name',
                'direction' => 'asc',
            ],
        ]);
        $query->setPage($page);
        $query->setPageSize($limit);

        return $this->getResponse($query);
    }
}
