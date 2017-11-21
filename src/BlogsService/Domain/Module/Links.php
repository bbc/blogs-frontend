<?php
declare(strict_types = 1);

namespace App\BlogsService\Domain\Module;

class Links implements ModuleInterface
{
    /** @var string */
    private $title;

    /** @var string[][] */
    private $links;

    public function __construct(string $title, array $links)
    {
        $this->title = $title;
        $this->links = $links;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    /**
     * Gets the value of links.
     *
     * @return string[][]
     */
    public function getLinks(): array
    {
        return $this->links;
    }
}
