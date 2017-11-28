<?php
declare(strict_types = 1);

namespace App\Ds\ContentBlock\ImageBlock;

use App\BlogsService\Domain\ContentBlock\Image;
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

    public function getImageUrl(): string
    {
        return $this->image->getImage()->getUrl(640);
    }

    public function getCaption(): string
    {
        return $this->image->getCaption();
    }
}
