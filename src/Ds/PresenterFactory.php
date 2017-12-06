<?php
declare(strict_types = 1);

namespace App\Ds;

use App\BlogsService\Domain\Author;
use App\BlogsService\Domain\Blog;
use App\BlogsService\Domain\Image;
use App\BlogsService\Domain\Post;
use App\BlogsService\Domain\Module\FreeText;
use App\BlogsService\Domain\Module\Links;
use App\BlogsService\Domain\Tag;
use App\Ds\Author\AuthorSummary\AuthorSummaryPresenter;
use App\Ds\Molecule\AuthorAtoZ\AuthorAtoZPresenter;
use App\Ds\Molecule\Image\ImagePresenter;
use App\Ds\Post\Author\AuthorPresenter;
use App\Ds\Post\PostFull\PostFullPresenter;
use App\Ds\Post\PostPreview\PostPreviewPresenter;
use App\Ds\Post\PostSummary\PostSummaryPresenter;
use App\Ds\Post\SocialBar\SocialBarPresenter;
use App\Ds\Post\Tags\TagsPresenter;
use App\Ds\SidebarModule\AboutPresenter;
use App\Ds\SidebarModule\BlogTagsPresenter;
use App\Ds\SidebarModule\FreetextPresenter;
use App\Ds\SidebarModule\LinksPresenter;
use App\Ds\SidebarModule\UpdatesPresenter;
use App\Translate\TranslateProvider;
use App\ValueObject\CosmosInfo;

/**
 * Ds Factory Class for creating presenters.
 */
class PresenterFactory
{
    /** @var CosmosInfo */
    private $cosmosInfo;

    public function __construct(CosmosInfo $cosmosInfo)
    {
        $this->cosmosInfo = $cosmosInfo;
    }

    public function authorAtoZPresenter(
        Blog $blog,
        array $options = []
    ): AuthorAtoZPresenter {
        return new AuthorAtoZPresenter(
            $blog,
            $options
        );
    }

    public function authorSummaryPresenter(
        Author $author,
        string $blogId,
        int $postCount,
        array $options = []
    ): AuthorSummaryPresenter {
        return new AuthorSummaryPresenter(
            $author,
            $blogId,
            $postCount,
            $options
        );
    }

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

    public function postFullPresenter(
        array $contentBlocks,
        array $options = []
    ): PostFullPresenter {
        return new PostFullPresenter($contentBlocks, $this->cosmosInfo, $options);
    }

    public function postPreviewPresenter(
        Blog $blog,
        Post $post,
        int $charLimit,
        array $options = []
    ): PostPreviewPresenter {
        return new PostPreviewPresenter($this->cosmosInfo, $blog, $post, $charLimit, $options);
    }

    public function postSocialBarPresenter(
        Post $post,
        string $blogId,
        array $options = []
    ): SocialBarPresenter {
        return new SocialBarPresenter($post, $blogId, $options);
    }

    public function postSummaryPresenter(
        Blog $blog,
        Post $post,
        array $options = []
    ): PostSummaryPresenter {
        return new PostSummaryPresenter($blog, $post, $options);
    }

    public function postTagsPresenter(
        array $tags,
        string $blogId,
        array $options = []
    ): TagsPresenter {
        return new TagsPresenter($tags, $blogId, $options);
    }

    public function updatesModulePresenter(
        Blog $blog,
        ?Tag $tag,
        array $options = []
    ): UpdatesPresenter {
        return new UpdatesPresenter(
            $blog,
            $tag,
            $options
        );
    }
}
