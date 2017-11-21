<?php
declare(strict_types=1);

namespace App\BlogsService\Query\IsiteQuery;

class FileIdQuery implements QueryInterface
{
    /** @var (int|string)[] */
    private $parameters = [];

    public function setProject(string $project): self
    {
        $this->parameters['project'] = 'blogs-' . $project;
        return $this;
    }

    public function setId(string $id): self
    {
        $this->parameters['id'] = $id;
        return $this;
    }

    public function setDepth(int $depth): self
    {
        $this->parameters['depth'] = $depth;
        return $this;
    }

    public function getPath(): string
    {
        return '/content/file?' . http_build_query($this->parameters);
    }
}
