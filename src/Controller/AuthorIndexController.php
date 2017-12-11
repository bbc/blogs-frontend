<?php
declare(strict_types = 1);

namespace App\Controller;

use App\BlogsService\Domain\Author;
use App\BlogsService\Domain\Blog;
use App\BlogsService\Service\AuthorService;
use App\BlogsService\Service\PostService;
use App\Ds\Molecule\Paginator\PaginatorPresenter;
use Symfony\Component\HttpFoundation\Request;

class AuthorIndexController extends BlogsBaseController
{
    public function __invoke(Request $request, Blog $blog, AuthorService $authorService, PostService $postService)
    {
        $this->setBlog($blog);

        $page = $this->getPageNumber($request);

        $authorsResult = $authorService->getAuthorsByBlog($blog, $page);

        /** @var Author[] $authors */
        $authors = $authorsResult->getDomainModels();

        $authorPostResults = $postService->getPostsForAuthors($blog, $authors, 1, 1);

        $paginator = null;
        if ($authorsResult->getTotal() > $authorsResult->getPageSize()) {
            $paginator = new PaginatorPresenter($authorsResult->getPage(), $authorsResult->getPageSize(), $authorsResult->getTotal());
        }

        return $this->renderWithChrome(
            'author/index.html.twig',
            [
                'authorPostResults' => $authorPostResults,
                'authors' => $authors,
                'paginatorPresenter' => $paginator,
                'showAZ' => $paginator !== null,
            ]
        );
    }

    protected function getIstatsPageType(): string
    {
        return 'author_index';
    }
}