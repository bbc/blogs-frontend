<?php
declare(strict_types = 1);

namespace App\Controller;

use App\BlogsService\Domain\Blog;
use App\BlogsService\Service\PostService;
use App\BlogsService\Service\TagService;
use App\Ds\Molecule\Paginator\PaginatorPresenter;
use Symfony\Component\HttpFoundation\Request;

class TagShowController extends BlogsBaseController
{
    public function __invoke(Request $request, Blog $blog, string $tagId, TagService $tagService, PostService $postService)
    {
        $this->setBlog($blog);

        $tag = $tagService->getTagById($tagId, $blog);

        if (!$tag) {
            throw $this->createNotFoundException('Tag not found');
        }

        $page = (int) $request->query->get('page', 1);
        if ($page < 1) {
            $page = 1;
        }

        $postResults = $postService->getPostsByTag(
            $blog,
            $tag,
            $page,
            10
        );

        if ($postResults->getTotal() === 0) {
            throw $this->createNotFoundException('No posts were found for the tag ' . $tag->getName());
        }

        $paginator = null;
        if ($postResults->getTotal() > $postResults->getPageSize()) {
            $paginator = new PaginatorPresenter($postResults->getPage(), $postResults->getPageSize(), $postResults->getTotal());
        }

        return $this->renderWithChrome(
            'tag/show.html.twig',
            [
                'tag' => $tag,
                'postResults' => $postResults,
                'paginatorPresenter' => $paginator,
            ]
        );
    }
}