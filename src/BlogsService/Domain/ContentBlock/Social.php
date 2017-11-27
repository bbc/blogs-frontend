<?php
declare(strict_types = 1);

namespace App\BlogsService\Domain\ContentBlock;

class Social extends AbstractContentBlock
{
    /** @var string  */
    private $link;

    /** @var string */
    private $alt;

    public function __construct(string $link, string $alt)
    {
        $this->link = $link;
        $this->alt = $alt;
    }

    public function getLink(): string
    {
        return $this->link;
    }

    public function getAlt(): string
    {
        return $this->alt;
    }
}
