<?php
declare(strict_types = 1);

namespace App\Controller;

use App\BlogsService\Domain\Blog;
use App\BlogsService\Service\PostService;
use App\BlogsService\Service\TagService;
use Symfony\Component\HttpFoundation\Request;

class TagIndexController extends BlogsBaseController
{
    public function __invoke(Request $request, Blog $blog, TagService $tagService, PostService $postService)
    {
        $this->setBlog($blog);
        $this->counterName = 'tags';

        $page = $this->getPageNumber($request);

        $this->otherIstatsLabels = ['page' => (string) $page];

        $tagsResult = $tagService->getTagsByBlog($blog, $page, 10);

        $tagPostCounts = $postService->getPostCountsForTags($blog, $tagsResult->getDomainModels());

        $paginator = $this->createPaginator($tagsResult);

        return $this->renderWithChrome(
            'tag/index.html.twig',
            [
                'tagResult' => $tagsResult,
                'tagPostCounts' => $tagPostCounts,
                'paginatorPresenter' => $paginator,
            ]
        );
    }

    protected function getIstatsPageType(): string
    {
        return 'tag_index';
    }
}
