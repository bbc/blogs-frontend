<?php
declare(strict_types = 1);

namespace App\Controller;

use App\BlogsService\Domain\Author;
use App\BlogsService\Domain\Blog;
use App\BlogsService\Service\AuthorService;
use App\BlogsService\Service\PostService;
use Symfony\Component\HttpFoundation\Request;

class AuthorShowAtoZController extends BlogsBaseController
{
    public function __invoke(Request $request, Blog $blog, string $letter, AuthorService $authorService, PostService $postService)
    {
        $this->setIstatsPageType('author_letter');
        $this->analyticsHelper()->setChapterOneVariable('list-authors');
        $this->setBlog($blog);
        $this->counterName = 'authors';

        $page = $this->getPageNumber($request);

        $this->otherIstatsLabels = ['page' => (string) $page];

        $authorsResult = $authorService->getAuthorsByLetter($blog, $letter, $page);

        /** @var Author[] $authors */
        $authors = $authorsResult->getDomainModels();

        $authorPostResults = $postService->getPostsForAuthors($blog, $authors, 1, 1);
        $paginator = $this->createPaginator($authorsResult);

        return $this->renderWithChrome(
            'author/index.html.twig',
            [
                'authorPostResults' => $authorPostResults,
                'authors' => $authors,
                'paginatorPresenter' => $paginator,
                'showAZ' => true,
            ]
        );
    }
}
