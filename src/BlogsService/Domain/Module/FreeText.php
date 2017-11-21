<?php
declare(strict_types = 1);

namespace App\BlogsService\Domain\Module;

use App\BlogsService\Domain\Image;

class FreeText implements ModuleInterface
{
    /** @var string */
    private $body;

    /** @var Image */
    private $image;

    /** @var string */
    private $title;

    public function __construct(string $title, string $body, Image $image = null)
    {
        $this->title = $title;
        $this->image = $image;
        $this->body = $body;
    }

    public function getBody(): string
    {
        return $this->body;
    }

    public function getImage(): ?Image
    {
        return $this->image;
    }

    public function getTitle(): string
    {
        return $this->title;
    }
}
