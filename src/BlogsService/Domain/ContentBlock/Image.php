<?php
declare(strict_types=1);

namespace App\BlogsService\Domain\ContentBlock;

use App\BlogsService\Domain\Image as DomainImage;

class Image extends AbstractContentBlock
{
    /** @var Image */
    private $image;

    /** @var string */
    private $caption;

    public function __construct(DomainImage $image = null, string $caption = '')
    {
        $this->image = $image;
        $this->caption = $caption;
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
