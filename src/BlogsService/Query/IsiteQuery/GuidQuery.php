<?php
declare(strict_types=1);

namespace App\BlogsService\Query\IsiteQuery;

class GuidQuery implements QueryInterface
{
    /** @var (int|string)[] */
    private $parameters = [];

    public function setProject(string $project): self
    {
        $this->parameters['project'] = 'blogs-' . $project;
        return $this;
    }

    public function setContentId(string $id): self
    {
        $this->parameters['contentId'] = $id;
        return $this;
    }

    public function setDepth(int $depth): self
    {
        $this->parameters['depth'] = $depth;
        return $this;
    }

    public function getPath(): string
    {
        return '/content?' . http_build_query($this->parameters);
    }

    public function setPreview(bool $preview)
    {
        $this->parameters['preview'] = $preview ? 'true' : 'false';
        $this->parameters['allowNonLive'] = $preview ? 'true' : 'false';
        return $this;
    }
}
