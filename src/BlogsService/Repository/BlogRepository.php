<?php
declare(strict_types = 1);
namespace App\BlogsService\Repository;

use App\BlogsService\Query\IsiteQuery\FileIdQuery;
use App\BlogsService\Query\IsiteQuery\SearchQuery;
use Psr\Http\Message\ResponseInterface;

class BlogRepository extends AbstractRepository
{
    public function getAllBlogs(): ?ResponseInterface
    {
        $query = new SearchQuery();
        $query->setSearchChildrenOfProject('blogs')
            ->setFileType('blogsmetadata')
            ->setQuery(["or" => [['blog-name', 'contains', '*']]])
            ->setSort([["elementPath" => "/*:form/*:metadata/*:blog-name"]])
            ->setDepth(0)
            ->setUnfiltered(true);

        return $this->getResponse($query);
    }

    public function getBlogById(string $blogId): ?ResponseInterface
    {
        $query = new FileIdQuery();
        $query->setProject($blogId)
            ->setId('blogs-meta-data')
            ->setDepth(2);

        return $this->getResponse($query);
    }
}
