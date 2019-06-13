<?php
declare(strict_types = 1);

namespace App\Controller;

use App\BlogsService\Domain\Blog;
use App\BlogsService\Service\PostService;
use App\BlogsService\Service\TagService;
use Symfony\Component\HttpFoundation\Request;

class TagShowController extends BlogsBaseController
{
    public function __invoke(Blog $blog, string $tagId, TagService $tagService, PostService $postService)
    {
        $tag = $tagService->getTagById($tagId, $blog);

        if (!$tag) {
            throw $this->createNotFoundException('Tag not found');
        }

        $page = $this->getPageNumber();

        $postResults = $postService->getPostsByTag(
            $blog,
            $tag,
            $page,
            10
        );

        $paginator = $this->createPaginator($postResults);

        $analyticsLabels = $this->atiAnalyticsHelper()->makeLabels('tag', $blog);
        $pageMetadata = $this->pageMetadataHelper()->makePageMetadata(
            'View all posts tagged with ' . $tag->getName() . ' on the BBC\'s "' . $blog->getName() . '" blog',
            $blog
        );

        return $this->renderBlogPage(
            'tag/show.html.twig',
            $analyticsLabels,
            $pageMetadata,
            $blog,
            [
                'tag' => $tag,
                'postResults' => $postResults,
                'paginatorPresenter' => $paginator,
            ]
        );
    }
}
