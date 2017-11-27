<?php
declare(strict_types = 1);

namespace App\Ds\ContentBlock\ImageBlock;

use App\BlogsService\Domain\ContentBlock\Image;
use App\Ds\Presenter;

class ImageBlockPresenter extends Presenter
{
    /** @var Image */
    private $content;

    public function __construct(Image $content, array $options = [])
    {
        parent::__construct($options);
        $this->content = $content;
    }
}

