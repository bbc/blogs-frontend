<?php
declare(strict_types = 1);

namespace App\Ds;

use App\BlogsService\Domain\Blog;
use App\BlogsService\Domain\Image;
use App\BlogsService\Domain\Module\FreeText;
use App\BlogsService\Domain\Module\Links;
use App\Ds\Molecule\Image\ImagePresenter;
use App\Ds\SidebarModule\AboutPresenter;
use App\Ds\SidebarModule\BlogTagsPresenter;
use App\Ds\SidebarModule\FreetextPresenter;
use App\Ds\SidebarModule\LinksPresenter;

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

    public function blogTagsModulePresenter(
        Blog $blog,
        array $tags,
        array $options = []
    ): BlogTagsPresenter {
        return new BlogTagsPresenter(
            $blog,
            $tags,
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

    public function linksModulePresenter(
        Links $module,
        array $options = []
    ): LinksPresenter {
        return new LinksPresenter(
            $module,
            $options
        );
    }
}
