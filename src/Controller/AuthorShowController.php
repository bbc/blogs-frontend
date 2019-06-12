<?php
declare(strict_types = 1);

namespace App\Controller;

use App\BlogsService\Domain\Blog;
use App\BlogsService\Domain\ValueObject\GUID;
use App\BlogsService\Service\AuthorService;
use App\BlogsService\Service\PostService;
use Symfony\Component\HttpFoundation\Request;

class AuthorShowController extends BlogsBaseController
{
    public function __invoke(Request $request, Blog $blog, string $guid, AuthorService $authorService, PostService $postService)
    {
        $this->setIstatsPageType('author_show');
        $this->analyticsHelper()->setChapterOneVariable('author');
        $this->setBlog($blog);

        $this->pageContextHelper()->setAllowPreview();
        $author = $authorService->getAuthorByGUID(new GUID($guid), $blog, $this->pageContextHelper()->isPreview());

        if (!$author) {
            throw $this->createNotFoundException('Author not found');
        }

        $this->counterName = 'authors.' . $author->getName();

        $page = $this->getPageNumber($request);

        $this->otherIstatsLabels = [
            'author_name' => $author->getName(),
            'author_role' => $author->getRole(),
            'author_description' => $author->getDescription(),
            'page' => (string) $page,
        ];

        $postResult = $postService->getPostsByAuthor($blog, $author, $page);

        $paginator = $this->createPaginator($postResult);

        return $this->renderWithChrome(
            'author/show.html.twig',
            ['author' => $author, 'paginatorPresenter' => $paginator, 'postResult' => $postResult]
        );
    }
}
