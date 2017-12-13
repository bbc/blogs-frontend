<?php
declare(strict_types = 1);

namespace App\Controller;

use App\BlogsService\Domain\Blog;
use App\BlogsService\Service\TagService;
use Symfony\Component\HttpFoundation\Request;

class TagIndexController extends BlogsBaseController
{
    public function __invoke(Request $request, Blog $blog, TagService $tagService)
    {
        $this->setBlog($blog);
        $this->counterName = 'tags';

        $page = $this->getPageNumber($request);

        $this->otherIstatsLabels = ['page' => (string) $page];

        $tagsResult = $tagService->getTagsByBlog($blog, $page, 10);

        $paginator = $this->createPaginator($tagsResult);

        return $this->renderWithChrome(
            'tag/index.html.twig',
            [
                'tagResult' => $tagsResult,
                'paginatorPresenter' => $paginator,
            ]
        );
    }

    protected function getIstatsPageType(): string
    {
        return 'tag_index';
    }
}
