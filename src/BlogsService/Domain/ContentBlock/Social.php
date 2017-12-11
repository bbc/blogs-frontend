<?php
declare(strict_types = 1);

namespace App\BlogsService\Domain\ContentBlock;

/**
 * This is a content block, which authors use to embed objects from
 * external sites, for example a Tweet or a Vimeo video
 */
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

    public function getCharacterCount(): int
    {
        // This is a default value for the purposes of post truncation
        return 200;
    }

    public function getLink(): string
    {
        return $this->link;
    }

    /**
     * For accessibility and fallback for Javascript embed
     */
    public function getAlt(): string
    {
        return $this->alt;
    }
}
