<?php
declare(strict_types = 1);

namespace App\Ds;

use App\BlogsService\Domain\Blog;
use App\BlogsService\Domain\Image;
use App\BlogsService\Domain\Module\FreeText;
use App\Ds\Molecule\Image\ImagePresenter;
use App\Ds\SidebarModule\AboutPresenter;
use App\Ds\SidebarModule\FreetextPresenter;

/**
 * Ds Factory Class for creating presenters.
 */
class PresenterFactory
{
    public function aboutModulePresenter(
        Blog $blog,
        array $options = []
    ): AboutPresenter {
        return new AboutPresenter(
            $blog,
            $options
        );
    }

    public function freetextModulePresenter(
        FreeText $module,
        array $options = []
    ): FreetextPresenter {
        return new FreetextPresenter(
            $module,
            $options
        );
    }

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
