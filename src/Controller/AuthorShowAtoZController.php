<?php
declare(strict_types = 1);

namespace App\Controller;

use App\BlogsService\Domain\Author;
use App\BlogsService\Domain\Blog;
use App\BlogsService\Service\AuthorService;
use App\BlogsService\Service\PostService;
use App\Ds\Molecule\Paginator\PaginatorPresenter;
use Symfony\Component\HttpFoundation\Request;

class AuthorShowAtoZController extends BlogsBaseController
{
    public function __invoke(Request $request, Blog $blog, string $letter, AuthorService $authorService, PostService $postService)
    {
        $this->setBlog($blog);
        $this->counterName = 'authors';

        $page = (int) $request->query->get('page', 1);
        if ($page < 1) {
            $page = 1;
        }

        $this->otherIstatsLabels = ['page' => (string) $page];

        $authorsResult = $authorService->getAuthorsByLetter($blog, $letter, $page);
        /** @var Author[] $authors */
        $authors = $authorsResult->getDomainModels();

        $authorPostResults = [];
        foreach ($authors as $author) {
            $authorFileId = (string) $author->getFileId();

            $authorPostResults[$authorFileId] = $postService->getPostsByAuthor(
                $blog,
                $author,
                1,
                1
            );
        }

        $paginator = null;
        if ($authorsResult->getTotal() > $authorsResult->getPageSize()) {
            $paginator = new PaginatorPresenter($authorsResult->getPage(), $authorsResult->getPageSize(), $authorsResult->getTotal());
        }

        return $this->renderWithChrome(
            'author/show_atoz.html.twig',
            [
                'authorPostResults' => $authorPostResults,
                'authors' => $authors,
                'paginatorPresenter' => $paginator,
            ]
        );
    }

    protected function getIstatsPageType(): string
    {
        return 'author_letter';
    }
}
