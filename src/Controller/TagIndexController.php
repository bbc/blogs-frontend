<?php
declare(strict_types = 1);

namespace App\Controller;

use App\BlogsService\Domain\Blog;
use App\BlogsService\Service\PostService;
use App\BlogsService\Service\TagService;

class TagIndexController extends BlogsBaseController
{
    public function __invoke(Blog $blog, TagService $tagService, PostService $postService)
    {
        $page = $this->getPageNumber();

        $tagsResult = $tagService->getTagsByBlog($blog, $page, 10);

        $tagPostCounts = $postService->getPostCountsForTags($blog, $tagsResult->getDomainModels());

        $paginator = $this->createPaginator($tagsResult);

        $analyticsLabels = $this->atiAnalyticsHelper()->makeLabels('list-tags', 'index-category', $blog);

        $pageMetadata = $this->pageMetadataHelper()->makePageMetadata(
            'A list of tags on the BBC\'s ' . $this->pageMetadataHelper()->blogNameForDescription($blog),
            $blog
        );

        return $this->renderBlogPage(
            'tag/index.html.twig',
            $analyticsLabels,
            $pageMetadata,
            $blog,
            [
                'tagResult' => $tagsResult,
                'tagPostCounts' => $tagPostCounts,
                'paginatorPresenter' => $paginator,
            ]
        );
    }
}
