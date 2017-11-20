<?php
declare(strict_types = 1);
namespace App\Ds;

use App\BlogsService\Domain\Image;
use App\Ds\Molecule\Image\ImagePresenter;

/**
 * Ds Factory Class for creating presenters.
 */
class PresenterFactory
{
    public function imagePresenter(
        Image $image,
        int $defaultWidth,
        $sizes,
        array $options = []
    ): ImagePresenter {
        return new ImagePresenter(
            $image,
            $defaultWidth,
            $sizes,
            $options
        );
    }
}
