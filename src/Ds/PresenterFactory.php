<?php
declare(strict_types = 1);

namespace App\Ds;

use App\BlogsService\Domain\Author;
use App\BlogsService\Domain\Blog;
use App\BlogsService\Domain\Image;
use App\BlogsService\Domain\Post;
use App\BlogsService\Domain\Module\FreeText;
use App\BlogsService\Domain\Module\Links;
use App\Ds\Molecule\Image\ImagePresenter;
use App\Ds\Post\Author\AuthorPresenter;
use App\Ds\Post\Social\SocialPresenter;
use App\Ds\Post\Tags\TagsPresenter;
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

    public function postAuthorPresenter(
        Author $author,
        string $blogId,
        array $options = []
    ): AuthorPresenter {
        return new AuthorPresenter($author, $blogId, $options);
    }

    public function postSocialPresenter(
        Post $post,
        string $blogId,
        array $options = []
    ): SocialPresenter {
        return new SocialPresenter($post, $blogId, $options);
    }

    public function postTagsPresenter(
        array $tags,
        string $blogId,
        array $options = []
    ): TagsPresenter {
        return new TagsPresenter($tags, $blogId, $options);
    }
}
