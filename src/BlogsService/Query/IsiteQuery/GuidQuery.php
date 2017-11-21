<?php
declare(strict_types=1);

namespace App\BlogsService\Query\IsiteQuery;

class GuidQuery
{
    /** @var string[] */
    private $parameters = [];

    public function setProject(string $project): GuidQuery
    {
        $this->parameters['project'] = 'blogs-' . $project;
        return $this;
    }

    public function setContentId(string $id): GuidQuery
    {
        $this->parameters['contentId'] = $id;
        return $this;
    }

    public function setDepth(int $depth): GuidQuery
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
