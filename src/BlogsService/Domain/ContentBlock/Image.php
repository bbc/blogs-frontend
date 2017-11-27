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

    /** @var bool */
    private $hasCaption;

    public function __construct(DomainImage $image = null, string $caption = null)
    {
        $this->image = $image;
        $this->caption = $caption;

        if (empty($caption)) {
            $this->hasCaption = false;
        } else {
            $this->hasCaption = true;
        }
    }

    public function getImage(): Image
    {
        return $this->image;
    }

    public function getCaption(): string
    {
        return $this->caption;
    }

    public function hasCaption(): bool
    {
        return $this->hasCaption;
    }
}
