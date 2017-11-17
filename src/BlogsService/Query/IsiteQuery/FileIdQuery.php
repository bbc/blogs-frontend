<?php
declare(strict_types=1);

namespace App\BlogsService\Query\IsiteQuery;

class FileIdQuery
{

    private $parameters = [];

    public function setProject(string $project)
    {
        $this->parameters['project'] = $project;
        return $this;
    }

    public function setId(string $id)
    {
        $this->parameters['id'] = $id;
        return $this;
    }

    public function setDepth(int $depth)
    {
        $this->parameters['depth'] = (string) $depth;
        return $this;
    }

    public function getParameters(): array
    {
        return $this->parameters;
    }
}
