<?php
declare(strict_types = 1);

namespace App\Ds\ContentBlock\ImageBlock;

use App\BlogsService\Domain\ContentBlock\Image;
use App\BlogsService\Domain\Image as DomainImage;
use App\Ds\Presenter;

class ImageBlockPresenter extends Presenter
{
    /** @var Image */
    private $image;

    public function __construct(Image $image, array $options = [])
    {
        parent::__construct($options);
        $this->image = $image;
    }

    public function getImage(): DomainImage
    {
        return $this->image->getImage();
    }

    public function getCaption(): string
    {
        return $this->image->getCaption();
    }
}
