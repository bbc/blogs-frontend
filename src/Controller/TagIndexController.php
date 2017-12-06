<?php
declare(strict_types = 1);

namespace App\Controller;

use App\BlogsService\Domain\Blog;
use App\BlogsService\Service\TagService;
use App\Ds\Molecule\Paginator\PaginatorPresenter;
use Symfony\Component\HttpFoundation\Request;

class TagIndexController extends BlogsBaseController
{
    public function __invoke(Request $request, Blog $blog, TagService $tagService)
    {
        $this->setBlog($blog);

        $page = $this->getPageNumber($request);

        $tagsResult = $tagService->getTagsByBlog($blog, $page, 10);

        $paginator = null;
        if ($tagsResult->getTotal() > $tagsResult->getPageSize()) {
            $paginator = new PaginatorPresenter($tagsResult->getPage(), $tagsResult->getPageSize(), $tagsResult->getTotal());
        }

        return $this->renderWithChrome('tag/index.html.twig', ['tagResult' => $tagsResult, 'paginatorPresenter' => $paginator]);
    }
}
