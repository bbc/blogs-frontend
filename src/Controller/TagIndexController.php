<?php
declare(strict_types = 1);

namespace App\Controller;

use App\BlogsService\Domain\Blog;
use App\BlogsService\Service\PostService;
use App\BlogsService\Service\TagService;
use Symfony\Component\HttpFoundation\Request;

class TagIndexController extends BlogsBaseController
{
    public function __invoke(Blog $blog, TagService $tagService, PostService $postService)
    {
        $this->setIstatsPageType('tag_index');
        $this->analyticsHelper()->setChapterOneVariable('list-tags');
        $this->setBlog($blog);
        $this->counterName = 'tags';

        $page = $this->getPageNumber();

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
}
