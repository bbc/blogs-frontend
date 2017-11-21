<?php
declare(strict_types=1);

namespace App\BlogsService\Query\IsiteQuery;

class FileIdQuery
{
    /** @var string[] */
    private $parameters = [];

    public function setProject(string $project): FileIdQuery
    {
        $this->parameters['project'] = 'blogs-' . $project;
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
