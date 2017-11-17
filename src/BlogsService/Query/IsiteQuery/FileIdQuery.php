<?php
declare(strict_types=1);

namespace App\BlogsService\Query\IsiteQuery;

use App\BlogsService\Domain\Blog;

class FileIdQuery
{
    /** @var string[] */
    private $parameters = [];

    public function setProject(string $project): FileIdQuery
    {
        $this->parameters['project'] = Blog::BLOG_PREFIX . $project;
        return $this;
    }

    public function setId(string $id): FileIdQuery
    {
        $this->parameters['id'] = $id;
        return $this;
    }

    public function setDepth(int $depth): FileIdQuery
    {
        $this->parameters['depth'] = (string) $depth;
        return $this;
    }

    /** @return string[] */
    public function getParameters(): array
    {
        return $this->parameters;
    }
}
