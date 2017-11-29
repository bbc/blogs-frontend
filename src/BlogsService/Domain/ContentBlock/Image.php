<?php
declare(strict_types=1);

namespace App\BlogsService\Domain\ContentBlock;

use App\BlogsService\Domain\Image as DomainImage;

class Image extends AbstractContentBlock
{
    /** @var DomainImage */
    private $image;

    /** @var string */
    private $caption;

    public function __construct(DomainImage $image, string $caption)
    {
        $this->image = $image;
        $this->caption = $caption;
    }

    public function getCharacterCount(): int
    {
        // This is a default value for the purposes of post truncation
        // TODO check this when implementing post truncation

        return 200;
    }

    public function getImage(): DomainImage
    {
        return $this->image;
    }

    public function getCaption(): string
    {
        return $this->caption;
    }
}
